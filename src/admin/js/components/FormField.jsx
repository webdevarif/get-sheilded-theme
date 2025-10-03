import React from 'react';

const FormField = ({
    label,
    children,
    help,
    required = false,
    className = '',
    style = {},
}) => {
    const containerStyle = {
        marginBottom: '20px',
        ...style,
    };

    const labelStyle = {
        display: 'block',
        marginBottom: '8px',
        fontWeight: '600',
        color: '#1d2327',
        fontSize: '14px',
        lineHeight: '1.4',
    };

    const helpStyle = {
        fontSize: '13px',
        color: '#646970',
        marginTop: '8px',
        marginBottom: '0',
        lineHeight: '1.4',
    };

    return (
        <div className={className} style={containerStyle}>
            {label && (
                <label style={labelStyle}>
                    {label}
                    {required && <span style={{ color: '#dc3232', marginLeft: '4px' }}>*</span>}
                </label>
            )}
            {children}
            {help && <p style={helpStyle}>{help}</p>}
        </div>
    );
};

export default FormField;
