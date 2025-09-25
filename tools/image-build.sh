#!/bin/bash

set -x

build() {
    local type="$1"
    local name="$2"
    local build_path
    local build_target

    if [ "$type" = "base" ]; then
        build_path="./base/${name}"
        build_target="icb-"
    elif [ "$type" = "chall" ]; then
        build_path="./challenges/${name}/build"
        build_target="ic-"
    else
        echo "Unknown type: $type"
        return 1
    fi

    echo "Building [$type/$name] image=$build_target$name, path=$build_path"

    docker build -t "$build_target$name" "$build_path"
}

build base alpine-build
build base debian-build
build base nc-chall
build base apache-php

build chall sanity-check
build chall quick-scale-studio
build chall l2d-guess
build chall students-list
build chall login-sql
build chall cve-2023-38831
build chall leak-password
build chall ruby-on-rails
build chall not-strict-file-upload