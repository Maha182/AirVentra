from flask import Flask, jsonify

app = Flask(__name__)

@app.route("/submitData", methods=["GET", "POST"])
def submitData():
    data = {'id': 'M1', 'zone_name': 'Dry Zone'}
    return jsonify(data)  # Send JSON response

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)