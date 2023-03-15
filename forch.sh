#!/bin/bash
echo "Bienvenue sur oFork"
echo "Cette ligne de commande va te permettre de faire un fork de ton projet d'apothéose sur un repository personnel"

# On demande si le repo perso a été créé
echo -n "As-tu créé le repository sur ton compte personnel ? (Oui/Non)"
read persoRepoCreated

if [ "$persoRepoCreated" = "Oui" ] || [ "$persoRepoCreated" = "oui" ]; then
    echo "Parfait, on passe à la suite alors."
elif [ "$persoRepoCreated" = "Non" ] || [ "$persoRepoCreated" = "non" ]; then
    echo -e "\033[0;31m Alors commence par créer ton repo sur ton compte perso Github : https://github.com/new \033[0m"
    exit 0
else
    echo -e "\033[0;31m Il faut taper soit Oui ou Non! Et pas : $persoRepoCreated \033[0m"
    exit 0
fi

# On demande le lien du repo qui va accueillir le repo à forker
echo -n "Entre le lien du repository qui va accueillir ton projet d'apothéose : https://github.com/Dorian-Guignard/DG"
read newRepoGitHubProject

# On récupère le nom du dossier
OLDIFS=$IFS
IFS='/'
cptLoop=0
newProjectDirectoryName=''
read -ra ADDR <<< "$newRepoGitHubProject"
for i in "${ADDR[@]}"; do
    ((cptLoop++))
    if [ "$cptLoop" = 5 ]; then
        newProjectDirectoryName=$i
    fi
done
IFS=$OLDIFS

# On demande le lien du repo de projet dans l'organisation O'clock
echo -n "Entre le lien du repository de ton projet d'apothéose que tu veux forker : https://github.com/O-clock-Lucy/projet-12-recettes-healthy-back/tree/master"
read repoGitHubProjectOclock

# On clone le repo d'apothéose
git clone "$repoGitHubProjectOclock" "$newProjectDirectoryName"

# On se déplace dans le dossier du repo
cd "$newProjectDirectoryName"

# On récupère toutes les branches du repo
for BRANCH in $(git branch -a | grep remotes | grep -v HEAD | grep -v master); do
  git branch --track "${BRANCH#remotes/origin/}" "${BRANCH}";
done

# On supprime la remote origin
git remote remove origin

# On ajoute la nouvelle remote
git remote add origin "$newRepoGitHubProject"

# On push sur le nouveau repo avec toutes les branches
git push --all

# On remonte dans le dossier de travail de base
cd ..

# On supprime le dossier en local
rm -rf "$newProjectDirectoryName"

echo "Ton repo est maintenant transférer sur ton compte perso : $newRepoGitHubProject"