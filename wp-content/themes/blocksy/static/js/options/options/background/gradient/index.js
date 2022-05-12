/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
import { __, sprintf } from 'ct-i18n'
import { createElement, useCallback, useMemo } from '@wordpress/element'

import {
	__experimentalHeading as Heading,
	__experimentalVStack as VStack,
} from '@wordpress/components'

/**
 * Internal dependencies
 */
import CircularOptionPicker from './circular-option-picker'
import CustomGradientPicker from './custom-gradient-picker'

function SingleOrigin({
	className,
	clearGradient,
	gradients,
	onChange,
	value,
	actions,
	content,
}) {
	const gradientOptions = useMemo(() => {
		return (gradients || []).map(({ gradient, name }) => (
			<CircularOptionPicker.Option
				key={gradient}
				value={gradient}
				isSelected={value === gradient}
				tooltipText={
					name ||
					// translators: %s: gradient code e.g: "linear-gradient(90deg, rgba(98,16,153,1) 0%, rgba(172,110,22,1) 100%);".
					sprintf(__('Gradient code: %s'), gradient)
				}
				style={{ color: 'rgba( 0,0,0,0 )', background: gradient }}
				onClick={
					value === gradient
						? clearGradient
						: () => onChange(gradient)
				}
				aria-label={
					name
						? // translators: %s: The name of the gradient e.g: "Angular red to blue".
						  sprintf(__('Gradient: %s'), name)
						: // translators: %s: gradient code e.g: "linear-gradient(90deg, rgba(98,16,153,1) 0%, rgba(172,110,22,1) 100%);".
						  sprintf(__('Gradient code: %s'), gradient)
				}
			/>
		))
	}, [gradients, value, onChange, clearGradient])
	return (
		<CircularOptionPicker
			className={className}
			options={gradientOptions}
			actions={actions}>
			{content}
		</CircularOptionPicker>
	)
}

function MultipleOrigin({
	className,
	clearGradient,
	gradients,
	onChange,
	value,
	actions,
	content,
}) {
	return (
		<VStack spacing={3} className={className}>
			{(gradients || []).map(
				({ name, gradients: gradientSet }, index) => {
					return (
						<VStack spacing={2} key={index}>
							<Heading>{name}</Heading>
							<SingleOrigin
								clearGradient={clearGradient}
								gradients={gradientSet}
								onChange={onChange}
								value={value}
								{...(gradients.length === index + 1
									? {
											actions,
											content,
									  }
									: {})}
							/>
						</VStack>
					)
				}
			)}
		</VStack>
	)
}

export default function GradientPicker({
	className,
	gradients,
	onChange,
	value,
	clearable = true,
	disableCustomGradients = false,
	__experimentalHasMultipleOrigins,
	__experimentalIsRenderedInSidebar,
}) {
	const clearGradient = useCallback(() => onChange(undefined), [onChange])
	const Component =
		__experimentalHasMultipleOrigins && gradients?.length
			? MultipleOrigin
			: SingleOrigin

	return (
		<Component
			className={className}
			clearable={clearable}
			clearGradient={clearGradient}
			gradients={gradients}
			onChange={onChange}
			value={value}
			actions={
				clearable &&
				(gradients?.length || !disableCustomGradients) && (
					<CircularOptionPicker.ButtonAction onClick={clearGradient}>
						{__('Clear')}
					</CircularOptionPicker.ButtonAction>
				)
			}
			content={
				!disableCustomGradients && (
					<CustomGradientPicker
						__experimentalIsRenderedInSidebar={
							__experimentalIsRenderedInSidebar
						}
						value={value}
						onChange={onChange}
					/>
				)
			}
		/>
	)
}
