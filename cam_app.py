import cv2
from flask import Flask, Response, jsonify
from pyzbar.pyzbar import decode
from ultralytics import YOLO
import torch
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

device = 'cuda' if torch.cuda.is_available() else 'cpu'
barcode_model = YOLO('best_yolo11.pt')
# barcode_model = YOLO('best.pt')
# Store unique barcodes
unique_barcodes = set()
last_detected_barcode = None  # Store the last detected barcode

def decode_barcodes(image_np):
    """Decode barcodes using Pyzbar."""
    barcodes = decode(image_np)
    return [{"data": barcode.data.decode('utf-8'), "type": barcode.type} for barcode in barcodes]

def process_frame(frame):
    """Process each frame: Detect barcodes using YOLO and decode with Pyzbar."""
    global unique_barcodes, last_detected_barcode
    image_np = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
    
    # Run YOLO detection with verbose=False to suppress unnecessary logs
    yolo_results = barcode_model(image_np, verbose=False)

    detected_barcodes = []
    
    for result in yolo_results:
        if not hasattr(result, 'boxes') or not result.boxes:
            return frame, detected_barcodes  # No detections, return frame immediately

        for box in result.boxes:
            x1, y1, x2, y2 = map(int, box.xyxy[0])
            cropped = image_np[y1:y2, x1:x2]  # Crop detected barcode region
            
            zbar_results = decode_barcodes(cropped)
            for zbar_result in zbar_results:
                barcode_data = zbar_result["data"]
                detected_barcodes.append(barcode_data)

                if barcode_data not in unique_barcodes:
                    unique_barcodes.add(barcode_data)
                    print(f"New Barcode Detected: {barcode_data}")  # Print only when a new barcode appears

                if last_detected_barcode != barcode_data:
                    last_detected_barcode = barcode_data

            # Draw bounding box
            cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)

    return frame, detected_barcodes

@app.route('/get_barcodes', methods=['GET'])
def get_barcodes():
    """Return the list of unique detected barcodes."""
    return jsonify({"barcodes": list(unique_barcodes)})

@app.route('/get_barcode', methods=['GET'])
def get_barcode():
    """Return the last detected barcode."""
    return jsonify({"barcode": last_detected_barcode})

def gen():
    # cap = cv2.VideoCapture(0)
    cap = cv2.VideoCapture('http://10.241.0.57:8080/video')
    if not cap.isOpened():
        print("Error: Cannot open camera")
        return
    
    while True:
        ret, frame = cap.read()
        if not ret:
            break

        frame, _ = process_frame(frame)
        ret, jpeg = cv2.imencode('.jpg', frame)
        if not ret:
            continue
        
        yield (b'--frame\r\n'
               b'Content-Type: image/jpeg\r\n\r\n' + jpeg.tobytes() + b'\r\n\r\n')
    
    cap.release()

@app.route('/video_feed')
def video_feed():
    return Response(gen(), mimetype='multipart/x-mixed-replace; boundary=frame')

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000, use_reloader=False)
