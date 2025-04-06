import cv2
import numpy as np
import matplotlib.pyplot as plt
import matplotlib.animation as animation
import random
import networkx as nx

# Load image
image = cv2.imread('warehouse.jpg')
if image is None:
    raise FileNotFoundError("Image not found. Check the file path.")

# Get image dimensions
height, width, _ = image.shape
print(f"Image Width: {width} pixels, Height: {height} pixels")

# Scale: assume warehouse width = 50 meters
scale_factor = 50 / width  # meters per pixel
print(f"Updated Scale Factor: {scale_factor:.4f} meters per pixel")

# Convert to HSV for color detection
hsv = cv2.cvtColor(image, cv2.COLOR_BGR2HSV)

# Detect light brown boxes
lower_brown = np.array([10, 50, 50])
upper_brown = np.array([30, 255, 255])
mask = cv2.inRange(hsv, lower_brown, upper_brown)

# Morphological cleanup
kernel = np.ones((3, 3), np.uint8)
mask = cv2.morphologyEx(mask, cv2.MORPH_OPEN, kernel, iterations=1)
mask = cv2.morphologyEx(mask, cv2.MORPH_CLOSE, kernel, iterations=1)

# Find contours
contours, _ = cv2.findContours(mask, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)

# Extract center points of boxes
box_locations = []
for contour in contours:
    if cv2.contourArea(contour) > 100:
        M = cv2.moments(contour)
        if M["m00"] != 0:
            cx = int(M["m10"] / M["m00"])
            cy = int(M["m01"] / M["m00"])
            box_locations.append((cx, cy))

print("Detected Box Locations:", box_locations)

# --- Christofides Algorithm for TSP ---

def calculate_distance(p1, p2):
    return np.linalg.norm(np.array(p1) - np.array(p2))

def minimum_spanning_tree(locations):
    G = nx.Graph()
    for i, loc1 in enumerate(locations):
        for j, loc2 in enumerate(locations):
            if i != j:
                G.add_edge(i, j, weight=calculate_distance(loc1, loc2))
    mst = nx.minimum_spanning_tree(G)

    # Store positions as node attributes
    for i, loc in enumerate(locations):
        mst.nodes[i]['pos'] = loc  # Store position in the 'pos' attribute
    
    return mst

def find_odd_degree_vertices(mst):
    odd_degree_vertices = [v for v, degree in mst.degree() if degree % 2 != 0]
    return odd_degree_vertices

def perfect_matching(mst, odd_vertices):
    G = nx.Graph()
    for i in odd_vertices:
        for j in odd_vertices:
            if i != j:
                # Access the coordinates directly from mst.nodes[i] and mst.nodes[j]
                p1 = mst.nodes[i]['pos']
                p2 = mst.nodes[j]['pos']
                G.add_edge(i, j, weight=calculate_distance(p1, p2))
    matching = nx.algorithms.matching.max_weight_matching(G, maxcardinality=True)
    return matching

def create_eulerian_circuit(mst, matching):
    eulerian_circuit = list(mst.edges())
    for u, v in matching:
        if (u, v) not in eulerian_circuit and (v, u) not in eulerian_circuit:
            eulerian_circuit.append((u, v))
    return eulerian_circuit

def convert_to_hamiltonian(path, locations):
    visited = set()
    hamiltonian_path = []
    for u, v in path:
        if u not in visited:
            visited.add(u)
            hamiltonian_path.append(locations[u])
        if v not in visited:
            visited.add(v)
            hamiltonian_path.append(locations[v])
    return hamiltonian_path


if box_locations:
    mst = minimum_spanning_tree(box_locations)
    odd_vertices = find_odd_degree_vertices(mst)
    matching = perfect_matching(mst, odd_vertices)
    eulerian_circuit = create_eulerian_circuit(mst, matching)
    hamiltonian_path = convert_to_hamiltonian(eulerian_circuit, box_locations)
else:
    hamiltonian_path = []

# Compute total distance (in pixels)
total_distance_pixels = 0
for i in range(1, len(hamiltonian_path)):
    total_distance_pixels += np.linalg.norm(np.array(hamiltonian_path[i]) - np.array(hamiltonian_path[i-1]))

# Convert to meters (prototype scaling)
prototype_scale_factor = 50 / 500
total_distance_meters = total_distance_pixels * prototype_scale_factor
drone_speed_prototype = 1.5  # m/s
total_time_seconds_prototype = total_distance_meters / drone_speed_prototype


# Output results
print(f"Total Path Distance (in pixels): {total_distance_pixels:.2f} pixels")
print(f"Total Path Distance (scaled for Prototype): {total_distance_meters:.2f} meters")
print(f"Estimated Time to Complete Path (Prototype Warehouse): {total_time_seconds_prototype:.2f} seconds")

# Plot with animation
fig, ax = plt.subplots(figsize=(10, 8))
ax.imshow(cv2.cvtColor(image, cv2.COLOR_BGR2RGB))

# Scatter the boxes
if box_locations:
    ax.scatter(*zip(*box_locations), c='blue', marker='x', label='Detected Boxes')

# Initialize animated path
line, = ax.plot([], [], marker='o', color='red', linestyle='-', linewidth=2)

def init():
    line.set_data([], [])
    return line,

def update(frame):
    if frame < len(hamiltonian_path):
        x_vals, y_vals = zip(*hamiltonian_path[:frame+1])
        line.set_data(x_vals, y_vals)
    return line,

ani = animation.FuncAnimation(
    fig,
    update,
    frames=len(hamiltonian_path),
    init_func=init,
    blit=False,
    interval=2000,
    repeat=False
)

plt.legend()
plt.title("Christofides Algorithm Path Traversal for Drone")
plt.show()
