#
# Name: 
# Email ID: 
#

# If needed, you can define your own additional functions here.
# Start of your additional functions.


# End of your additional functions.

def find_stations_within_distance(mrt_map, orig, dist):
    # Modify the code below
    
    list_to_return = []
    index = 0
    #i need the orig station, check how many dist it can travel



    for lists in mrt_map:
    	if orig in lists:
    		
    		#find the index of where the orig is.
    		my_index = lists.index(orig)
    		if my_index == 0:
    			for i in range(1,dist+1):
    				list_to_return.append(lists[my_index+i])
    				
    		elif dist == 1:
    			list_to_return.append(lists[my_index-1])
    			list_to_return.append(lists[my_index+1])






    return list_to_return