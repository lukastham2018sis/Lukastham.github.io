#
# Name: 
# Email ID: 
#
def count_names_with_space(name_list):
    # Modify the code below
    num = 0
    if name_list == []:
    	return 0
    else:
    	for element in name_list:
    		if " " in element:
    			num += 1


    return num
