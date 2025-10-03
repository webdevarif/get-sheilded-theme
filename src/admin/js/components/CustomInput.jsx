import React from 'react';

const CustomInput = ({
    type = 'text',
    value,
    onChange,
    placeholder = '',
    min,
    max,
    step,
    disabled = false,
    className = '',
    style = {},
    ...props
}) => {
    const defaultStyle = {
        width: '100%',
        padding: '8px 12px',
        border: '1px solid #8c8f94',
        borderRadius: '4px',
        fontSize: '14px',
        color: '#1d2327',
        backgroundColor: '#fff',
        boxSizing: 'border-box',
        ...style,
    };

    return (
        <input
            type={type}
            value={value}
            onChange={onChange}
            placeholder={placeholder}
            min={min}
            max={max}
            step={step}
            disabled={disabled}
            className={className}
            style={defaultStyle}
            {...props}
        />
    );
};

export default CustomInput;
