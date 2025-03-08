# import subprocess

# # Start app_cam.py
# app_cam_process = subprocess.Popen(["python", "cam_app.py"])

# # Start assign_location.py
# assign_location_process = subprocess.Popen(["python", "assign_location.py"])
# # inventory = subprocess.Popen(["python", "inventorylevel.py"])

# # Wait for both processes to complete
# try:
#     app_cam_process.wait()
#     assign_location_process.wait()
# except KeyboardInterrupt:
#     print("Shutting down...")
#     app_cam_process.terminate()
#     # inventory.terminate()
#     assign_location_process.terminate()


from flask import Flask, request, jsonify
import subprocess
import psutil

app = Flask(__name__)

# Track process objects
processes = {
    "barcode": None,
    "assignment": None
}

def kill_process(proc_name):
    """Kill a process by name."""
    for proc in psutil.process_iter(['pid', 'name']):
        if proc_name in proc.info['name']:
            proc.kill()

@app.route('/start_service', methods=['POST'])
def start_service():
    """Start a service based on the request."""
    service = request.json.get("service")
    
    if service == "barcode" and processes["barcode"] is None:
        processes["barcode"] = subprocess.Popen(["python", "cam_app.py"])
        return jsonify({"message": "Barcode service started"})

    if service == "assignment" and processes["assignment"] is None:
        processes["assignment"] = subprocess.Popen(["python", "assign_location.py"])
        return jsonify({"message": "Storage assignment service started"})

    return jsonify({"message": f"{service} is already running or invalid service."})

@app.route('/stop_service', methods=['POST'])
def stop_service():
    """Stop a service based on the request."""
    service = request.json.get("service")
    
    if service in processes and processes[service]:
        processes[service].terminate()
        processes[service].wait()
        processes[service] = None
        kill_process(service)  # Ensure it's fully killed
        return jsonify({"message": f"{service} service stopped"})
    
    return jsonify({"message": f"{service} is not running or invalid service."})

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5002)

