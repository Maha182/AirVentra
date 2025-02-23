import subprocess

# Start app_cam.py
app_cam_process = subprocess.Popen(["python", "cam_app.py"])

# Start assign_location.py
# assign_location_process = subprocess.Popen(["python", "assign_location.py"])
inventory = subprocess.Popen(["python", "inventorylevel.py"])

# Wait for both processes to complete
try:
    app_cam_process.wait()
    inventory.wait()
    # assign_location_process.wait()
except KeyboardInterrupt:
    print("Shutting down...")
    app_cam_process.terminate()
    inventory.terminate()
    # assign_location_process.terminate()
