wraith capture configs/boundarycommission_capture.yaml > output.txt
wraith capture configs/industrialcourt_capture.yaml >> output.txt
wraith capture configs/pressclippings_capture.yaml >> output.txt
wraith capture configs/pacni_capture.yaml >> output.txt
wraith capture configs/interchange_capture.yaml >> output.txt
wraith capture configs/parole_capture.yaml >> output.txt
wraith capture configs/library_capture.yaml >> output.txt
grep -A3 'failed' output.txt
echo '************'
grep gallery output.txt
