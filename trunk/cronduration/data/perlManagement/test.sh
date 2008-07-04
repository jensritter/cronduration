#!/bin/bash
echo "HI" | ./post.pl START 
sleep 1
echo "Hi2" | ./post.pl STOP 


#find /bin -type f | while read loop
#do
#	echo "$loop"
#	echo $loop | ./post.pl START loop
#	echo $loop | ./post.pl stop  loop
#done
