import { createElement } from '@wordpress/element'
/**
 * WordPress dependencies
 */
import { useEffect, useRef } from '@wordpress/element'
import { Flex } from '@wordpress/components'
import { __experimentalUseDragging as useDragging } from '@wordpress/compose'

import COLORS from '../colors-values'
import CONFIG from '../config-values'

/**
 * Internal dependencies
 */

const CIRCLE_SIZE = 32
const INNER_CIRCLE_SIZE = 3

const space = (n) => `${n * 4}px`

function AngleCircle({ value, onChange, ...props }) {
	const angleCircleRef = useRef()
	const angleCircleCenter = useRef()
	const previousCursorValue = useRef()

	const setAngleCircleCenter = () => {
		const rect = angleCircleRef.current.getBoundingClientRect()
		angleCircleCenter.current = {
			x: rect.x + rect.width / 2,
			y: rect.y + rect.height / 2,
		}
	}

	const changeAngleToPosition = (event) => {
		const { x: centerX, y: centerY } = angleCircleCenter.current
		const { ownerDocument } = angleCircleRef.current
		// Prevent (drag) mouse events from selecting and accidentally
		// triggering actions from other elements.
		event.preventDefault()
		// Ensure the input isn't focused as preventDefault would leave it
		ownerDocument.activeElement.blur()
		onChange(getAngle(centerX, centerY, event.clientX, event.clientY))
	}

	const { startDrag, isDragging } = useDragging({
		onDragStart: (event) => {
			setAngleCircleCenter()
			changeAngleToPosition(event)
		},
		onDragMove: changeAngleToPosition,
		onDragEnd: changeAngleToPosition,
	})

	useEffect(() => {
		if (isDragging) {
			if (previousCursorValue.current === undefined) {
				previousCursorValue.current = document.body.style.cursor
			}
			document.body.style.cursor = 'grabbing'
		} else {
			document.body.style.cursor = previousCursorValue.current || null
			previousCursorValue.current = undefined
		}
	}, [isDragging])

	return (
		/* eslint-disable jsx-a11y/no-static-element-interactions */
		<div
			ref={angleCircleRef}
			onMouseDown={startDrag}
			className="components-angle-picker-control__angle-circle"
			style={{
				...(isDragging ? { cursor: 'grabbing' } : {}),
				borderRadius: '50%',
				border: `${CONFIG.borderWidth} solid ${COLORS.ui.border}`,
				boxSizing: 'border-box',
				cursor: 'grab',
				height: `${CIRCLE_SIZE}px`,
				overflow: `hidden`,
				width: `${CIRCLE_SIZE}px`,
			}}
			{...props}>
			<div
				style={{
					...(value ? { transform: `rotate(${value}deg)` } : {}),
					boxSizing: 'border-box',
					position: 'relative',
					width: '100%',
					height: '100%',
				}}
				className="components-angle-picker-control__angle-circle-indicator-wrapper">
				<div
					style={{
						background: COLORS.admin.theme,
						borderRadius: '50%',
						border: `${INNER_CIRCLE_SIZE}px solid ${COLORS.admin.theme}`,
						bottom: 0,
						boxSizing: 'border-box',
						display: 'block',
						height: 0,
						left: 0,
						margin: 'auto',
						position: 'absolute',
						right: 0,
						top: `-${CIRCLE_SIZE / 2}px`,
						width: 0,
					}}
					className="components-angle-picker-control__angle-circle-indicator"
				/>
			</div>
		</div>
		/* eslint-enable jsx-a11y/no-static-element-interactions */
	)
}

function getAngle(centerX, centerY, pointX, pointY) {
	const y = pointY - centerY
	const x = pointX - centerX

	const angleInRadians = Math.atan2(y, x)
	const angleInDeg = Math.round(angleInRadians * (180 / Math.PI)) + 90
	if (angleInDeg < 0) {
		return 360 + angleInDeg
	}
	return angleInDeg
}

export default AngleCircle
