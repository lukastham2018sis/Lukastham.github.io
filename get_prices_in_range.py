#
# Name: 
# Email ID: 
#
def get_prices_in_range(price_list, low, high):
    # Modify the code below    
    my_list = []

    for element in price_list:
    	if int(element) >= low and int(element) <= high:
    		my_list.append(element)

    return my_list	