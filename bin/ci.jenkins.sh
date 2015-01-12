#!/bin/sh

export SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )" # Do not edit this line

export db_host="localhost"
export db_user="root"
export db_pass="p4ssw0rd"
export db_name="website_creator"
export db_test_name="website_creator_test"
export base_url="http://ernani.mage.dev/"
export install_sample_data="no"

export magento_dir="magento"
export phpunit_filter=""

export MAGENTO_VERSION="magento-ce-1.9.0.1"

export BASE_DIR="$( dirname "${SCRIPT_DIR}" )"
export CI_LIB_DIR="${BASE_DIR}/magento-bash-ci"

sh ${CI_LIB_DIR}/ci-install.sh
sh ${CI_LIB_DIR}/ci-test.sh
