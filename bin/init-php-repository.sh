#! /bin/bash

# Resolve rootPath
scriptPath="${BASH_SOURCE[0]}"
while [ -h "$scriptPath" ]; do
  scriptDir="$( cd -P "$( dirname "$scriptPath" )" && pwd )"
  scriptPath="$(readlink "$scriptPath")"
  [[ $scriptPath != /* ]] && scriptPath="$scriptDir/$scriptPath"
done
scriptDir="$( cd -P "$( dirname "$scriptPath" )" && pwd )"
rootPath="$( cd -P "$scriptDir/../" && pwd )"

#
# - Template paths
rootTemplatePath="$rootPath/templates"
defaultTemplatePath="$rootTemplatePath/default"
defaultLibraryTemplatePath="$rootTemplatePath/library/default"
symfonyLibraryTemplatePath="$rootTemplatePath/library/symfony"
projectTemplatePath="$rootTemplatePath/project"

name="$(pwd | sed "s#$(cd ../ && pwd)/##")"

# DEBUG
name="BehatUtilsExtension"
# END -DEBUG


# - Git variables
gitUsername=`git config --global user.name`;

# DEBUG
gitUsername=' test '
# END - DEBUG

    # Remove leading / trailing spaces
gitUsername="$(echo "$gitUsername" | sed 's#^\s+##g')"
if [[ -z "$gitUsername" ]]; then
    echo "Git username cannot be empty ! Use git config user.name 'NAME' to define it"
    exit 1
fi
    # Ensure CamelCase style for git username
gitUsername="$(echo "$gitUsername" | perl -pe 's/(-|_|\s)([a-z])/$1\U$2/g' | perl -pe 's/^([a-z])/\U$1/g')"
githubRepositoryUrlId="$(git remote -v show -n origin | grep 'Fetch URL: ' | sed 's#.*github\.com:\(.*\)\.git#\1#')"
githubRepositoryUrl="github.com/$githubRepositoryUrlId"

# - Composer variables
composerPackageName="$(echo "$githubRepositoryUrlId" | perl -pe 's/([a-z0-9])([A-Z])/$1-\L$2/g' | perl -pe 's/^([A-Z])/\L$1/g')"

# - Autoloading variables
autoloadNamespace="$(echo "$githubRepositoryUrlId" | perl -pe 's/(-|_)([a-zA-Z])/\U$2/g' | perl -pe 's/^([a-z])/\U$1/g' | sed 's#/#\\#g')"
AutoloadPsr0Namespace="$(echo "$autoloadNamespace" | sed 's#\\#\\\\#g')"
AutoloadPsr4Namespace="${AutoloadPsr0Namespace}\\\\"


echo "rootTemplatePath='$rootTemplatePath'"
echo "defaultTemplatePath='$defaultTemplatePath'"
echo "defaultLibraryTemplatePath='$defaultLibraryTemplatePath'"
echo "symfonyLibraryTemplatePath='$symfonyLibraryTemplatePath'"
echo "projectTemplatePath='$projectTemplatePath'"
echo "name='$name'"

echo "gitUsername='$gitUsername'"
echo "githubRepositoryUrlId='$githubRepositoryUrlId'"
echo "githubRepositoryUrl='$githubRepositoryUrl'"

echo "composerPackageName='$composerPackageName'"

echo "autoloadNamespace='$autoloadNamespace'"
echo "AutoloadPsr0Namespace='$AutoloadPsr0Namespace'"
echo "AutoloadPsr4Namespace='$AutoloadPsr4Namespace'"
exit

# Init Repository
# LICENSE
sed \
    -e "s#%LICENSE_YEAR%#`date +%Y`#g" \
    -e "s#%licenseAuthor%#$gitUsername#g" $defaultTemplatePath/LICENSE > ./LICENSE
# README
sed \
    -e "s#%name%#$name#g" \
    -e "s#%githubRepositoryUrlId%#$githubRepositoryUrlId#g" \
    -e "s#%composerPackageName%#$composerPackageName#g" $defaultLibraryTemplatePath/README.md > ./README.md
# CONTRIBUTING
sed \
    -e "s#%name%#$name#g" \
    -e "s#%githubRepositoryUrlId%#$githubRepositoryUrlId#g" $defaultTemplatePath/CONTRIBUTING.md > ./CONTRIBUTING.md

# Add Git related files
cp $defaultLibraryTemplatePath/.gitignore ./.gitignore


# Add composer configuration
cp $defaultLibraryTemplatePath/composer.yml ./composer.yml

# Add tests configuration files
cp $defaultLibraryTemplatePath/phpcs.xml.dist ./phpcs.xml.dist
cp $defaultLibraryTemplatePath/phpunit.xml.dist ./phpunit.xml.dist
cp $defaultLibraryTemplatePath/behat.yml ./behat.yml

# Add continuous integration configuration
cp $defaultLibraryTemplatePath/.scrutinizer.yml ./.scrutinizer.yml
cp $defaultLibraryTemplatePath/.travis.yml ./.travis.yml
