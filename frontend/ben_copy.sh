#!/bin/bash

for file in ~/IT490GroupRepo/frontend/*;
do
    sudo cp $file /var/www/sample
    echo "$file has been copied to /var/www/sample" 
    echo "complete!"
done