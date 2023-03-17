#!/usr/bin/env zx

/*
* Script to release the seats.io java lib.
*   - changes the version number in README.md
*   - changes the version number in build.gradle
*   - creates the release in Gihub (using gh cli)
*
*
* Prerequisites:
*   - zx installed (https://github.com/google/zx)
*   - gh cli installed (https://cli.github.com/)
*   - semver cli installed (https://github.com/fsaintjacques/semver-tool)
*
* Usage:
*   zx ./release.mjs -v major/minor -n "release notes"
* */

// don't output the commands themselves
$.verbose = false

const versionToBump = getVersionToBump()
const latestVersion = await fetchLatestReleasedVersionNumber()

await pullLastVersion().then(release)

function getVersionToBump() {
    if (!argv.v || !(argv.v === 'minor' || argv.v === 'major')) {
        throw new Error ("Please specify -v major/minor")
    }
    return argv.v
}

function removeLeadingV(tagName) {
    if (tagName.startsWith('v')) {
        return tagName.substring(1)
    }
    return tagName
}

async function fetchLatestReleasedVersionNumber() {
    let result = await $`gh release view --json tagName`
    let tagName = JSON.parse(result).tagName
    return removeLeadingV(tagName)
}

async function determineNextVersionNumber(previous) {
    return (await $`semver bump ${versionToBump} ${previous}`).stdout.trim()
}

async function pullLastVersion() {
    await $`git checkout master`
    await $`git pull origin master`
}

async function release() {
    const nextVersion = await determineNextVersionNumber(latestVersion)
    const newTag = 'v' + nextVersion
    return await $`gh release create ${newTag} --generate-notes`.catch(error => {
        console.error('something went wrong while creating the release. Please revert the version change!')
        throw error
    })
}