wraith capture configs/boundarycommission_capture.yaml > output.txt
wraith capture configs/pressclippings_capture.yaml >> output.txt
wraith capture configs/pacni_capture.yaml >> output.txt
wraith capture configs/interchange_capture.yaml >> output.txt
wraith capture configs/parole_capture.yaml >> output.txt
wraith capture configs/library_capture.yaml >> output.txt
wraith capture configs/lgbc_capture.yaml >> output.txt
wraith capture configs/sem_capture.yaml >> output.txt
wraith capture configs/nibureau_capture.yaml >> output.txt
wraith capture configs/judiciary_capture.yaml >> output.txt
grep -A3 'failed' output.txt
echo '************'
grep gallery output.txt
