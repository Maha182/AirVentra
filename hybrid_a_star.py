import airsim
import numpy as np
import heapq
import time

# Connect to the AirSim drone
client = airsim.MultirotorClient()
client.confirmConnection()
client.enableApiControl(True)
client.armDisarm(True)
client.takeoffAsync().join()

# Define the warehouse grid
warehouse_map = np.zeros((20, 20))  # 20x20 grid warehouse
obstacles = [(5, 5), (6, 5), (7, 5), (10, 10)]  # Example obstacles

for obs in obstacles:
    warehouse_map[obs] = 1  # Mark obstacles on the grid

# Hybrid A* Heuristic Function
def heuristic(a, b):
    return np.linalg.norm(np.array(a) - np.array(b))

# Hybrid A* Path Planning
def hybrid_a_star(start, goal, grid):
    open_list = []
    heapq.heappush(open_list, (0, start))
    came_from = {start: None}
    cost_so_far = {start: 0}

    while open_list:
        _, current = heapq.heappop(open_list)

        if current == goal:
            break

        # Drone movement in 8 directions
        for dx, dy in [(-1, 0), (1, 0), (0, -1), (0, 1), (-1, -1), (-1, 1), (1, -1), (1, 1)]:
            next_pos = (current[0] + dx, current[1] + dy)

            if 0 <= next_pos[0] < grid.shape[0] and 0 <= next_pos[1] < grid.shape[1] and grid[next_pos] == 0:
                new_cost = cost_so_far[current] + 1
                if next_pos not in cost_so_far or new_cost < cost_so_far[next_pos]:
                    cost_so_far[next_pos] = new_cost
                    priority = new_cost + heuristic(goal, next_pos)
                    heapq.heappush(open_list, (priority, next_pos))
                    came_from[next_pos] = current

    path = []
    current = goal
    while current is not None:
        path.append(current)
        current = came_from[current]
    path.reverse()
    return path

# Set Start and Goal
start_pos = (1, 1)
goal_pos = (15, 15)
path = hybrid_a_star(start_pos, goal_pos, warehouse_map)

# Convert path to AirSim drone movement
for waypoint in path:
    x, y = waypoint
    client.moveToPositionAsync(x, y, -2, 2).join()

# Land the drone
client.landAsync().join()
client.armDisarm(False)
client.enableApiControl(False)
