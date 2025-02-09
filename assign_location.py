from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route("/getData", methods=["POST"])
def getData():
    # Get the product description from the JSON POST request
    data = request.get_json()
    description = data.get('description')  # Product description
    
    # Do something with the product description (e.g., log, store, or process it)
    print(f"Description: {description}")

    # Simulate finding zone_name based on the description
    zone_name = "Dry Zone"  # For example purposes, this could be dynamic

    # Return the zone name
    return jsonify({"zone_name": zone_name})

if __name__ == '__main__':
    app.run(debug=True, port=5000)
