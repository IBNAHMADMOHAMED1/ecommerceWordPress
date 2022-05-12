export const clamp = (min, max, value) => Math.max(min, Math.min(max, value))

export const computeLinearScale = (domain, range, value) =>
	range[0] +
	((range[1] - range[0]) / (domain[1] - domain[0])) * (value - domain[0])

const getRowInitialMinHeight = (el) => {
	const elComp = getComputedStyle(el)
	return parseFloat(elComp.getPropertyValue('--height'))
}

export const getRowInitialHeight = (el) => {
	if (el.blcInitialHeight) {
		return el.blcInitialHeight
	}

	let initialHeight = el.firstElementChild.firstElementChild.getBoundingClientRect()
		.height

	el.blcInitialHeight = initialHeight

	return initialHeight
}

export const getRowStickyHeight = (el) => {
	let styles = getComputedStyle(el)

	let maybeShrink = 100

	if (el.dataset.row.includes('middle')) {
		maybeShrink = styles.getPropertyValue('--sticky-shrink')
	}

	let rowStickyHeight = getRowInitialHeight(el)
	let finalInitialHeight = 0

	// if (el.querySelector('.site-logo-container')) {
	// 	let computedLogo = getComputedStyle(
	// 		el.querySelector('.site-logo-container')
	// 	)

	// 	let logoInitialHeight = parseFloat(
	// 		computedLogo.getPropertyValue('--logo-max-height') || '50px'
	// 	)

	// 	let logoStickyShrink = parseFloat(
	// 		computedLogo.getPropertyValue('--logo-sticky-shrink') || '1'
	// 	)

	// 	if (logoStickyShrink < 1) {
	// 		let rowInitialMinHeight = getRowInitialMinHeight(el)

	// 		if (maybeShrink) {
	// 			rowInitialMinHeight *= parseFloat(maybeShrink) / 100
	// 		}

	// 		let logoStickyHeight = logoInitialHeight * logoStickyShrink

	// 		let finalInitialHeight =
	// 			rowStickyHeight - logoInitialHeight + logoStickyHeight

	// 		return Math.max(rowInitialMinHeight, finalInitialHeight)
	// 	}
	// }

	if (maybeShrink) {
		rowStickyHeight *= parseFloat(maybeShrink) / 100
	}

	return rowStickyHeight
}
