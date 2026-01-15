#!/bin/bash
clear
echo "========================================================================"
echo "Por favor ingrese el texto para el commit que va a realizar: "
read commit
echo "========================================================================"

git add .
git branch -M main
git commit -m "$commit"
git push -u origin main
