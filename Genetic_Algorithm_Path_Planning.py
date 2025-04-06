import cv2
import numpy as np
import matplotlib.pyplot as plt
import matplotlib.animation as animation
import random

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

# --- Genetic Algorithm for TSP ---

def calculate_distance(path, locations):
    return sum(np.linalg.norm(np.array(locations[path[i]]) - np.array(locations[path[i+1]]))
               for i in range(len(path) - 1))

def create_population(size, num_nodes):
    return [random.sample(range(num_nodes), num_nodes) for _ in range(size)]

def crossover(parent1, parent2):
    start, end = sorted(random.sample(range(len(parent1)), 2))
    middle = parent1[start:end]
    child = middle + [item for item in parent2 if item not in middle]
    return child

def mutate(path, mutation_rate=0.01):
    for i in range(len(path)):
        if random.random() < mutation_rate:
            j = random.randint(0, len(path) - 1)
            path[i], path[j] = path[j], path[i]
    return path

def evolve_population(population, locations, elite_size=2, mutation_rate=0.01):
    population = sorted(population, key=lambda path: calculate_distance(path, locations))
    next_generation = population[:elite_size]
    while len(next_generation) < len(population):
        parent1 = random.choice(population[:10])
        parent2 = random.choice(population[:10])
        child = crossover(parent1, parent2)
        next_generation.append(mutate(child, mutation_rate))
    return next_generation


if box_locations:
    num_generations = 200
    population_size = 100
    num_nodes = len(box_locations)
    population = create_population(population_size, num_nodes)

    for generation in range(num_generations):
        population = evolve_population(population, box_locations)
    
    best_path_indices = population[0]
    path = [box_locations[i] for i in best_path_indices]
else:
    path = []

# Compute total distance (in pixels)
total_distance_pixels = 0
for i in range(1, len(path)):
    total_distance_pixels += np.linalg.norm(np.array(path[i]) - np.array(path[i-1]))

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
    if frame < len(path):
        x_vals, y_vals = zip(*path[:frame+1])
        line.set_data(x_vals, y_vals)
    return line,

ani = animation.FuncAnimation(
    fig,
    update,
    frames=len(path),
    init_func=init,
    blit=False,
    interval=2000,
    repeat=False
)

plt.legend()
plt.title("Genetic Algorithm Path Traversal for Drone")
plt.show()
