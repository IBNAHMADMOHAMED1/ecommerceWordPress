import focusLock from 'dom-focus-lock'
import { isTouchDevice } from './is-touch-device'

export const focusLockOn = (container) => {
	let focusLockToUse = focusLock

	if (window.ctFrontend && window.ctFrontend.focusLock) {
		focusLockToUse = ctFrontend.focusLock
	} else {
		window.ctFrontend = window.ctFrontend || {}
		window.ctFrontend.focusLock = focusLockToUse
	}

	focusLockToUse.on(container)
}

export const focusLockOff = (container) => {
	let focusLockToUse = focusLock

	if (window.ctFrontend && window.ctFrontend.focusLock) {
		focusLockToUse = ctFrontend.focusLock
	} else {
		window.ctFrontend = window.ctFrontend || {}
		window.ctFrontend.focusLock = focusLockToUse
	}

	focusLockToUse.off(container)
}
