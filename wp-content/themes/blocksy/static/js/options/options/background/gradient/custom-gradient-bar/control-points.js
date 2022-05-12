/**
 * External dependencies
 */
import classnames from 'classnames'
import { colord } from 'colord'

/**
 * WordPress dependencies
 */
import { useInstanceId } from '@wordpress/compose'
import {
	useEffect,
	useRef,
	useState,
	useMemo,
	createElement,
} from '@wordpress/element'
import { plus } from '@wordpress/icons'
import { LEFT, RIGHT } from '@wordpress/keycodes'
import { Dropdown, Button, ColorPicker } from '@wordpress/components'

/**
 * Internal dependencies
 */
import { __, sprintf } from 'ct-i18n'
import {
	addControlPoint,
	clampPercent,
	removeControlPoint,
	updateControlPointColor,
	updateControlPointColorByPosition,
	updateControlPointPosition,
	getHorizontalRelativeGradientPosition,
} from './utils'
import {
	GRADIENT_MARKERS_WIDTH,
	MINIMUM_SIGNIFICANT_MOVE,
	KEYBOARD_CONTROL_POINT_VARIATION,
} from './constants'
import { normalizeColor } from '../../../../helpers/normalize-color'

export function CustomColorPickerDropdown({ isRenderedInSidebar, ...props }) {
	return (
		<Dropdown
			contentClassName={classnames(
				'components-color-palette__custom-color-dropdown-content',
				{
					'is-rendered-in-sidebar': isRenderedInSidebar,
				}
			)}
			{...props}
		/>
	)
}

function ControlPointButton({ isOpen, position, color, ...additionalProps }) {
	const instanceId = useInstanceId(ControlPointButton)
	const descriptionId = `components-custom-gradient-picker__control-point-button-description-${instanceId}`
	return (
		<>
			<Button
				aria-label={sprintf(
					// translators: %1$s: gradient position e.g: 70, %2$s: gradient color code e.g: rgb(52,121,151).
					__(
						'Gradient control point at position %1$s%% with color code %2$s.'
					),
					position,
					color
				)}
				aria-describedby={descriptionId}
				aria-haspopup="true"
				aria-expanded={isOpen}
				className={classnames(
					'components-custom-gradient-picker__control-point-button',
					{
						'is-active': isOpen,
					}
				)}
				style={{
					left: `${position}%`,
				}}
				{...additionalProps}
			/>
		</>
	)
}

function GradientColorPickerDropdown({
	isRenderedInSidebar,
	gradientPickerDomRef,
	...props
}) {
	const popoverProps = useMemo(() => {
		const result = {
			className:
				'components-custom-gradient-picker__color-picker-popover',
			position: 'top',
		}
		if (isRenderedInSidebar) {
			result.anchorRef = gradientPickerDomRef.current
			result.position = 'bottom left'
		}
		return result
	}, [gradientPickerDomRef.current, isRenderedInSidebar])
	return (
		<CustomColorPickerDropdown
			isRenderedInSidebar={isRenderedInSidebar}
			popoverProps={popoverProps}
			{...props}
		/>
	)
}

function ControlPoints({
	disableRemove,
	disableAlpha,
	gradientPickerDomRef,
	ignoreMarkerPosition,
	value: controlPoints,
	onChange,
	onStartControlPointChange,
	onStopControlPointChange,
	__experimentalIsRenderedInSidebar,
}) {
	const controlPointMoveState = useRef()

	const onMouseMove = (event) => {
		const relativePosition = getHorizontalRelativeGradientPosition(
			event.clientX,
			gradientPickerDomRef.current,
			GRADIENT_MARKERS_WIDTH
		)
		const {
			initialPosition,
			index,
			significantMoveHappened,
		} = controlPointMoveState.current
		if (
			!significantMoveHappened &&
			Math.abs(initialPosition - relativePosition) >=
				MINIMUM_SIGNIFICANT_MOVE
		) {
			controlPointMoveState.current.significantMoveHappened = true
		}

		onChange(
			updateControlPointPosition(controlPoints, index, relativePosition)
		)
	}

	const cleanEventListeners = () => {
		if (
			window &&
			window.removeEventListener &&
			controlPointMoveState.current &&
			controlPointMoveState.current.listenersActivated
		) {
			window.removeEventListener('mousemove', onMouseMove)
			window.removeEventListener('mouseup', cleanEventListeners)
			onStopControlPointChange()
			controlPointMoveState.current.listenersActivated = false
		}
	}

	useEffect(() => {
		return () => {
			cleanEventListeners()
		}
	}, [])

	return controlPoints.map((point, index) => {
		const initialPosition = point?.position
		return (
			ignoreMarkerPosition !== initialPosition && (
				<GradientColorPickerDropdown
					gradientPickerDomRef={gradientPickerDomRef}
					isRenderedInSidebar={__experimentalIsRenderedInSidebar}
					key={index}
					onClose={onStopControlPointChange}
					renderToggle={({ isOpen, onToggle }) => (
						<ControlPointButton
							key={index}
							onClick={() => {
								if (
									controlPointMoveState.current &&
									controlPointMoveState.current
										.significantMoveHappened
								) {
									return
								}
								if (isOpen) {
									onStopControlPointChange()
								} else {
									onStartControlPointChange()
								}
								onToggle()
							}}
							onMouseDown={() => {
								if (window && window.addEventListener) {
									controlPointMoveState.current = {
										initialPosition,
										index,
										significantMoveHappened: false,
										listenersActivated: true,
									}
									onStartControlPointChange()
									window.addEventListener(
										'mousemove',
										onMouseMove
									)
									window.addEventListener(
										'mouseup',
										cleanEventListeners
									)
								}
							}}
							onKeyDown={(event) => {
								if (event.keyCode === LEFT) {
									// Stop propagation of the key press event to avoid focus moving
									// to another editor area.
									event.stopPropagation()
									onChange(
										updateControlPointPosition(
											controlPoints,
											index,
											clampPercent(
												point.position -
													KEYBOARD_CONTROL_POINT_VARIATION
											)
										)
									)
								} else if (event.keyCode === RIGHT) {
									// Stop propagation of the key press event to avoid focus moving
									// to another editor area.
									event.stopPropagation()
									onChange(
										updateControlPointPosition(
											controlPoints,
											index,
											clampPercent(
												point.position +
													KEYBOARD_CONTROL_POINT_VARIATION
											)
										)
									)
								}
							}}
							isOpen={isOpen}
							position={point.position}
							color={point.color}
						/>
					)}
					renderContent={({ onClose }) => (
						<>
							<div
								className={
									wp.components.GradientPicker
										? 'ct-gutenberg-color-picker-new'
										: 'ct-gutenberg-color-picker'
								}>
								<ColorPicker
									enableAlpha={!disableAlpha}
									color={point.color}
									{...(wp.components.GradientPicker
										? {
												onChange: (color) => {
													onChange(
														updateControlPointColor(
															controlPoints,
															index,
															normalizeColor(
																color
															)
														)
													)
												},
										  }
										: {
												onChangeComplete: (result) => {
													onChange(
														updateControlPointColor(
															controlPoints,
															index,
															result.rgb.a === 1
																? result.hex
																: `rgba(${result.rgb.r}, ${result.rgb.g}, ${result.rgb.b}, ${result.rgb.a})`
														)
													)
												},
										  })}
								/>

								<div className="ct-color-picker-value">
									<input
										type="text"
										value={normalizeColor(point.color)}
										onChange={(e) => {
											onChange(
												updateControlPointColor(
													controlPoints,
													index,
													normalizeColor(
														e.target.value
													)
												)
											)
										}}
									/>
								</div>
							</div>

							{!disableRemove && controlPoints.length > 2 && (
								<Button
									className="components-custom-gradient-picker__remove-control-point"
									onClick={() => {
										onChange(
											removeControlPoint(
												controlPoints,
												index
											)
										)
										onClose()
									}}
									variant="link">
									{__('Remove Control Point')}
								</Button>
							)}
						</>
					)}
				/>
			)
		)
	})
}

function InsertPoint({
	value: controlPoints,
	onChange,
	onOpenInserter,
	onCloseInserter,
	insertPosition,
	disableAlpha,
	__experimentalIsRenderedInSidebar,
	gradientPickerDomRef,
}) {
	const [alreadyInsertedPoint, setAlreadyInsertedPoint] = useState(false)
	const [insertedColor, setInsertedColor] = useState('#fff')

	return (
		<GradientColorPickerDropdown
			gradientPickerDomRef={gradientPickerDomRef}
			isRenderedInSidebar={__experimentalIsRenderedInSidebar}
			className="components-custom-gradient-picker__inserter"
			onClose={() => {
				onCloseInserter()
			}}
			renderToggle={({ isOpen, onToggle }) => (
				<Button
					aria-expanded={isOpen}
					aria-haspopup="true"
					onClick={() => {
						if (isOpen) {
							onCloseInserter()
						} else {
							setAlreadyInsertedPoint(false)
							onOpenInserter()
						}
						onToggle()
					}}
					className="components-custom-gradient-picker__insert-point"
					icon={plus}
					style={{
						left:
							insertPosition !== null
								? `${insertPosition}%`
								: undefined,
					}}
				/>
			)}
			renderContent={() => (
				<div
					className={
						wp.components.GradientPicker
							? 'ct-gutenberg-color-picker-new'
							: 'ct-gutenberg-color-picker'
					}>
					<ColorPicker
						enableAlpha={!disableAlpha}
						color={insertedColor}
						{...(wp.components.GradientPicker
							? {
									onChange: (color) => {
										if (!alreadyInsertedPoint) {
											onChange(
												addControlPoint(
													controlPoints,
													insertPosition,
													normalizeColor(color)
												)
											)
											setAlreadyInsertedPoint(true)
										} else {
											onChange(
												updateControlPointColorByPosition(
													controlPoints,
													insertPosition,
													normalizeColor(color)
												)
											)
										}

										setInsertedColor(color)
									},
							  }
							: {
									onChangeComplete: (result) => {
										if (!alreadyInsertedPoint) {
											onChange(
												addControlPoint(
													controlPoints,
													insertPosition,
													result.rgb.a === 1
														? result.hex
														: `rgba(${result.rgb.r}, ${result.rgb.g}, ${result.rgb.b}, ${result.rgb.a})`
												)
											)
											setAlreadyInsertedPoint(true)
										} else {
											onChange(
												updateControlPointColorByPosition(
													controlPoints,
													insertPosition,
													result.rgb.a === 1
														? result.hex
														: `rgba(${result.rgb.r}, ${result.rgb.g}, ${result.rgb.b}, ${result.rgb.a})`
												)
											)
										}

										setInsertedColor(
											result.rgb.a === 1
												? result.hex
												: `rgba(${result.rgb.r}, ${result.rgb.g}, ${result.rgb.b}, ${result.rgb.a})`
										)
									},
							  })}
					/>

					<div className="ct-color-picker-value">
						<input
							value={normalizeColor(insertedColor)}
							type="text"
							onChange={({ target: { value: color } }) => {
								if (!alreadyInsertedPoint) {
									onChange(
										addControlPoint(
											controlPoints,
											insertPosition,
											normalizeColor(color)
										)
									)
									setAlreadyInsertedPoint(true)
								} else {
									onChange(
										updateControlPointColorByPosition(
											controlPoints,
											insertPosition,
											normalizeColor(color)
										)
									)
								}

								setInsertedColor(color)
							}}
						/>
					</div>
				</div>
			)}
		/>
	)
}
ControlPoints.InsertPoint = InsertPoint

export default ControlPoints
