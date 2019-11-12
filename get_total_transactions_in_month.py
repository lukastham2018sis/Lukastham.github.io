#
# Name: 
# Email ID: 
#

# If needed, you can define your own additional functions here.
# Start of your additional functions.


# End of your additional functions.

def get_total_transactions_in_month(trans_file, month):
    # Modify the code below
    new_months = []
    month_year = ''
    total_month = 0.0
    my_dict = {}
    
    with open(trans_file, 'r') as in_file:
    	for line in in_file:
    		line = line.rstrip("\n")
    		cols = line.split("\t")
    		
    		months = cols[0]
    		price = cols[1]
    		desc = cols[2]

    		new_months = months.split('/')
    		month_year = new_months[0] + "/" + new_months[2]
    		if month_year[0] != '0' or month_year[0] != '1' or month[0] != '0' or month[0] != '1':
    			if int(month_year[0]) > 2:
    				month_year = "0" + month_year
    			if int(month[0]) > 2:
    				month = "0" + month

    		if month_year not in my_dict:
    			my_dict[month_year] = [price]
    		else:
    			my_dict[month_year].append(price)



    	for key in my_dict:
    		for values in my_dict[key]:
    			if key == month:
    				total_month += float(values[1:])

    	return total_month


    
