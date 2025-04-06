import cv2
import numpy as np
import matplotlib.pyplot as plt
import matplotlib.animation as animation
from scipy.spatial.distance import cdist

# Load image
image = cv2.imread('warehouse.jpg')
if image is None:
    raise FileNotFoundError("Image not found. Check the file path.")

# Get image dimensions
height, width, _ = image.shape
print(f"Image Width: {width} pixels, Height: {height} pixels")

# Increase estimated warehouse width to 50 meters for a more realistic scale
scale_factor = 50 / width  # meters per pixel
print(f"Updated Scale Factor: {scale_factor:.4f} meters per pixel")

# Convert to HSV for better color segmentation
hsv = cv2.cvtColor(image, cv2.COLOR_BGR2HSV)

# Define color range for light brown boxes
lower_brown = np.array([10, 50, 50])
upper_brown = np.array([30, 255, 255])

# Create mask for brown boxes
mask = cv2.inRange(hsv, lower_brown, upper_brown)

# Clean up mask
kernel = np.ones((3, 3), np.uint8)
mask = cv2.morphologyEx(mask, cv2.MORPH_OPEN, kernel, iterations=1)
mask = cv2.morphologyEx(mask, cv2.MORPH_CLOSE, kernel, iterations=1)

# Find contours
contours, _ = cv2.findContours(mask, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)

# Extract box centers
box_locations = []
for contour in contours:
    if cv2.contourArea(contour) > 100:
        M = cv2.moments(contour)
        if M["m00"] != 0:
            cx = int(M["m10"] / M["m00"])
            cy = int(M["m01"] / M["m00"])
            box_locations.append((cx, cy))

# Check if we detected any boxes
print("Detected Box Locations:", box_locations)


# Implement Nearest Neighbor TSP
if box_locations:
    remaining_nodes = box_locations[:]
    path = [remaining_nodes.pop(0)]  # Start from the first detected node
    
    total_distance_pixels = 0  # Variable to store total path distance in pixels
    
    while remaining_nodes:
        last_node = path[-1]
        distances = cdist([last_node], remaining_nodes, metric='euclidean')
        nearest_index = np.argmin(distances)
        total_distance_pixels += distances[0, nearest_index]  # Add distance to total
        path.append(remaining_nodes.pop(nearest_index))
else:
    path = []
    total_distance_pixels = 0

# Convert distance to meters for prototype warehouse (only the prototype scaling)
prototype_scale_factor = 50 / 500  # Prototype's width is scaled (50 meters / 500 meters)

# Calculate the total path distance for the prototype
total_distance_pixels_prototype = total_distance_pixels * prototype_scale_factor

# Assume drone speed in prototype warehouse
drone_speed_prototype = 1.5  # meters per second (same drone speed for both real and prototype)

# Compute estimated time for the prototype warehouse
total_time_seconds_prototype = total_distance_pixels_prototype / drone_speed_prototype  # Time in seconds

print(f"Total Path Distance (in pixels): {total_distance_pixels:.2f} pixels")
print(f"Total Path Distance (scaled for Prototype): {total_distance_pixels_prototype:.2f} meters")
print(f"Estimated Time to Complete Path (Prototype Warehouse): {total_time_seconds_prototype:.2f} seconds")

# Plot the path with animation
fig, ax = plt.subplots(figsize=(10, 8))
ax.imshow(cv2.cvtColor(image, cv2.COLOR_BGR2RGB))

# Scatter the detected boxes
ax.scatter(*zip(*box_locations), c='blue', marker='x', label='Detected Boxes')

# Initialize the line that will be drawn
line, = ax.plot([], [], marker='o', color='red', linestyle='-', linewidth=2)

# Function to initialize the plot
def init():
    line.set_data([], [])
    return line,

# Update function for animation
def update(frame):
    if frame < len(path):
        x_vals, y_vals = zip(*path[:frame+1])
        line.set_data(x_vals, y_vals)
    return line,

# Create the animation with 2 seconds pause per node (interval=2000 ms)
ani = animation.FuncAnimation(
    fig, 
    update, 
    frames=len(path), 
    init_func=init, 
    blit=False, 
    interval=2000,  # 2000ms = 2 seconds for each node
    repeat=False  # Prevent the animation from repeating
)

# Display the animation
plt.legend()
plt.title("Optimized Path (TSP - Nearest Neighbor)")
plt.show()
