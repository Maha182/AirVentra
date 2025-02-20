import cv2
import requests
from flask import Flask, Response, jsonify
from pyzbar.pyzbar import decode
from ultralytics import YOLO
import torch
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

device = 'cuda' if torch.cuda.is_available() else 'cpu'
barcode_model = YOLO('barcode.pt')

API_URL = "http://127.0.0.1:8000/api/update_inventory"  # Laravel API endpoint

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
    image_np = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
    yolo_results = barcode_model(image_np)
    detected_barcodes = []
    
    if not yolo_results:
        return frame, detected_barcodes
    
    for result in yolo_results:
        if not hasattr(result, 'boxes'):
            continue
        
        for box in result.boxes:
            x1, y1, x2, y2 = map(int, box.xyxy[0])
            cropped = image_np[y1:y2, x1:x2]  # Crop detected barcode region
            
            zbar_results = decode_barcodes(cropped)
            for zbar_result in zbar_results:
                detected_barcodes.append(zbar_result["data"])
                
                # Send barcode to Laravel API for counting
                send_to_laravel(zbar_result["data"])

            cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)
    
    return frame, detected_barcodes

def send_to_laravel(barcode):
    """Send scanned barcode to Laravel API."""
    try:
        response = requests.post(API_URL, json={"barcode": barcode})
        if response.status_code == 200:
            print(f"Sent barcode {barcode} to Laravel: {response.json()}")
        else:
            print(f"Failed to send barcode {barcode}, Error: {response.text}")
    except Exception as e:
        print(f"Error sending barcode: {str(e)}")

def gen():
    cap = cv2.VideoCapture(0)
    # cap = cv2.VideoCapture('http://10.241.4.75:8080/video')
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
