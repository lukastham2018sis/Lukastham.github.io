#
# Name: 
# Email ID: 
#

# If needed, you can define your own additional functions here.
# Start of your additional functions.


# End of your additional functions.

def convert_to_list(num_list_str):
    # Modify the code below
    list_to_return = []

    for ch in num_list_str[1:]:
    	
    	if ch.isdigit():
    		list_to_return.append(ch)
    	if ch == '[':
    		my_index = num_list_str.find(ch)
    		if num_list_str[my_index+2] == '[':
    			list_to_return.append([num_list_str[my_index+1]])
    			num_list_str = (num_list_str[my_index:my_index+3:])
    			my_index3 = num_list_str.find('[')
    			my_index2 = num_list_str.find(']')
    			for char in num_list_str[my_index+1:my_index2-1]:

    				if char.isdigit():
    					list_to_return.append([char])
    				num_list_str = (num_list_str[my_index:my_index+3:])
    		else:
    			my_index2 = num_list_str.find(']')
    			for char in num_list_str[my_index+1:my_index2-1]:
    				if char.isdigit():
    					list_to_return.append([char])
    			num_list_str = num_list_str[my_index: my_index2+2:]	
    	if ch == ']':
    		num_list_str.strip(ch)



    return list_to_return
    
