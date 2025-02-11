import subprocess

# Start app_cam.py
app_cam_process = subprocess.Popen(["python", "cam_app.py"])

# Start assign_location.py
assign_location_process = subprocess.Popen(["python", "assign_location.py"])

# Wait for both processes to complete
try:
    app_cam_process.wait()
    assign_location_process.wait()
except KeyboardInterrupt:
    print("Shutting down...")
    app_cam_process.terminate()
    assign_location_process.terminate()
