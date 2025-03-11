from flask import Flask, request, jsonify
from bertopic import BERTopic

app = Flask(__name__)
mappings = {
    0: "beauty",
    1: "books",
    2: "electronics",
    3: "home",
    4: "grocery"
}

category_to_zone = {
    "beauty": "Cosmetic Zone",
    "books": "Dry Zone",
    "electronics": "Dry Zone",
    "home": "Bulk Zone",
    "grocery": "Refrigerator Zone"
}
model_path = "Bertopic_2"
topic_model = BERTopic.load(model_path, embedding_model="sentence-transformers/all-mpnet-base-v2")
@app.route("/getData", methods=["POST"])
def getData():
    
    #Get the product description from the JSON POST request
    data = request.get_json()
    if not data or "description" not in data:
        return jsonify({"error": "Missing 'description' in request body"}), 400

    description = data["description"]
    description = str(description).strip()
    if not isinstance(description, str) or not description.strip():
        return jsonify({"error": "Invalid or empty description"}), 400
    print(f"Description: {description}")


    print(topic_model.get_topic_info())

    # Check if the model is loaded
    if topic_model is None:
        return jsonify({"error": "Model not loaded"}), 500

    # Predict the topic
    predicted_topic, _ = topic_model.transform([description])  # Ensure input is a list
    print(f"Predicted topic: {predicted_topic}")
    category = mappings.get(predicted_topic[0], "Unknown")
    zone = category_to_zone.get(category, "Unknown Zone")
    print(f"{category}")
    print(f"{zone}")
    # Return the zone name
    return jsonify({"zone_name": zone})

    
if __name__ == '__main__':
    app.run(debug=True, port=5001, use_reloader=False)