#!/bin/bash
clear

fecha=`date +%d-%m-%Y`

echo "========================================================================"
echo "Ultimo commit realizado..."
git log -1

echo "========================================================================"
echo "Ingrese el nro de commit a realizar: "
read commit_number
echo "========================================================================"
echo "Ahora ingrese el texto para el commit NRO: $commit_number : "
read commit
echo "========================================================================"

git add .
git branch -M main
git commit -m "commit #$commit_number [ $commit ] - [ $fecha ]"
git push -u origin main
