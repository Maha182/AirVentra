from flask import Flask, jsonify, request
import requests

app = Flask(__name__)

@app.route('/call-laravel-api', methods=['POST'])  # Change to POST
def call_laravel():
    url = "http://127.0.0.1:8000/assign-storage"  # Laravel API URL
    headers = {"Content-Type": "application/json", "Accept": "application/json"}
    
    # Sample data to send in the request body
    data = {
        "zone_name": "ZoneA",
        "productID": 1
    }

    response = requests.post(url, json=data, headers=headers)

    return jsonify(response.json()), response.status_code  # Return Laravel's response

if __name__ == '__main__':
    app.run(debug=True)
