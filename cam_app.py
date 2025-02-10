from flask import Flask, render_template, Response
import cv2
import torch
from pyzbar.pyzbar import decode

app = Flask(__name__)

# Load YOLOv8 model (replace with your actual model)
model = torch.hub.load('ultralytics/yolov8', 'yolov8n')

def process_frame(frame):
    # YOLOv8 detection
    results = model(frame)
    
    # Decode barcodes from the frame
    barcodes = decode(frame)

    # Add bounding boxes for detected objects
    for result in results.pred[0]:
        x1, y1, x2, y2, conf, cls = result
        frame = cv2.rectangle(frame, (int(x1), int(y1)), (int(x2), int(y2)), (0, 255, 0), 2)

    # Draw barcode bounding boxes
    for barcode in barcodes:
        x, y, w, h = barcode.rect
        frame = cv2.rectangle(frame, (x, y), (x + w, y + h), (0, 0, 255), 2)

    return frame

def gen():
    # Replace with your phone's camera feed URL
    cap = cv2.VideoCapture(0) 
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

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/video_feed')
def video_feed():
    return Response(gen(), mimetype='multipart/x-mixed-replace; boundary=frame')

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)

