import { createElement } from '@wordpress/element'
/**
 * External dependencies
 */
import classnames from 'classnames'

/**
 * WordPress dependencies
 */
import { __ } from 'ct-i18n'

import {
	Flex,
	FlexBlock,
	FlexItem,
	__experimentalText as Text,
	__experimentalSpacer as Spacer,
	__experimentalInputControl as NumberControl,
} from '@wordpress/components'

/**
 * Internal dependencies
 */
import AngleCircle from './angle-circle'

// margin-bottom: 8px
const Root = Flex

const space = (n) => `${n * 4}px`

export default function AnglePickerControl({
	className,
	label = __('Angle'),
	onChange,
	value,
}) {
	const handleOnNumberChange = (unprocessedValue) => {
		const inputValue =
			unprocessedValue !== '' ? parseInt(unprocessedValue, 10) : 0
		onChange(inputValue)
	}

	const classes = classnames('components-angle-picker-control', className)

	return (
		<Root className={classes}>
			<FlexBlock>
				<NumberControl
					label={label}
					className="components-angle-picker-control__input-field"
					max={360}
					min={0}
					onChange={handleOnNumberChange}
					size="__unstable-large"
					step="1"
					value={value}
					hideHTMLArrows
					suffix={
						<Spacer
							as={Text}
							marginBottom={0}
							marginRight={space(3)}
							style={{
								color: 'var( --wp-admin-theme-color )',
							}}>
							°
						</Spacer>
					}
				/>
			</FlexBlock>
			<FlexItem
				style={{
					marginLeft: space(4),
					marginBottom: space(1),
					marginTop: 'auto',
				}}>
				<AngleCircle
					aria-hidden="true"
					value={value}
					onChange={onChange}
				/>
			</FlexItem>
		</Root>
	)
}
