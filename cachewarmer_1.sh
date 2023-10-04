#!/bin/bash

clear

echo "Warming up unity 3 sites with HTTrack ..."

echo "Warming Boundary Commission..."
httrack https://www.boundarycommission.org.uk/ -c5R2b0s0aqzvp0C0r3F "HTTrack cache builder" "-*.jpg" "-*.css" "-*.js" "-*.png" "-*.gif" "-*.jpeg" "-*.ico" "-*/type/*" "-*/topic/*" "-*/date/*" "-*page=*"

echo "Warming Industrial Court..."
httrack https://www.industrialcourt.gov.uk/ -c5R2b0s0aqzvp0C0r3F "HTTrack cache builder" "-*.jpg" "-*.css" "-*.js" "-*.png" "-*.gif" "-*.jpeg" "-*.ico" "-*/type/*" "-*/topic/*" "-*/date/*" "-*page=*"

echo "Warming Interchange NI..."
httrack https://www.interchangeni.org.uk/ -c5R2b0s0aqzvp0C0r3F "HTTrack cache builder" "-*.jpg" "-*.css" "-*.js" "-*.png" "-*.gif" "-*.jpeg" "-*.ico" "-*/type/*" "-*/topic/*" "-*/date/*" "-*page=*"

echo "---------------------------------------------------"

echo "Warming complete.:"
