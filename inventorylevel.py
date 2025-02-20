import cv2
from flask import Flask, Response, jsonify, request
from pyzbar.pyzbar import decode
from ultralytics import YOLO
import torch
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

device = 'cuda' if torch.cuda.is_available() else 'cpu'
barcode_model = YOLO('barcode.pt')

# Store unique barcodes
unique_barcodes = set()

def decode_barcodes(image_np):
    """Decode barcodes using Pyzbar."""
    barcodes = decode(image_np)
    decoded_info = []
    for barcode in barcodes:
        barcode_data = barcode.data.decode('utf-8')
        barcode_type = barcode.type
        decoded_info.append({"data": barcode_data, "type": barcode_type})
    return decoded_info

def process_frame(frame):
    """Process each frame: Detect barcodes using YOLO and decode with Pyzbar."""
    global unique_barcodes
    image_np = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
    yolo_results = barcode_model(image_np)
    
    if not yolo_results:
        return frame
    
    for result in yolo_results:
        if not hasattr(result, 'boxes'):
            continue
        
        for box in result.boxes:
            x1, y1, x2, y2 = map(int, box.xyxy[0])
            cropped = image_np[y1:y2, x1:x2]  # Crop detected barcode region
            
            zbar_results = decode_barcodes(cropped)
            for zbar_result in zbar_results:
                barcode_data = zbar_result["data"]
                unique_barcodes.add(barcode_data)
                print(f"Barcode detected: {barcode_data}")

            cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)
    
    return frame

@app.route('/get_barcodes', methods=['GET'])
def get_barcodes():
    """Return the list of unique detected barcodes."""
    return jsonify({"barcodes": list(unique_barcodes)})

def gen():
    cap = cv2.VideoCapture('http://192.168.100.78:8080/video')
    if not cap.isOpened():
        print("Error: Cannot open camera")
        return
    
    while True:
        ret, frame = cap.read()
        if not ret:
            break

        frame = process_frame(frame)
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
