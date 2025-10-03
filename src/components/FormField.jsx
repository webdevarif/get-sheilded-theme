import React from 'react';

const FormField = ({ 
    label, 
    help, 
    children, 
    required = false,
    className = '',
    style = {}
}) => {
    const defaultStyle = {
        marginBottom: '20px',
    };

    return (
        <div className={className} style={{ ...defaultStyle, ...style }}>
            {label && (
                <label style={{
                    display: 'block',
                    marginBottom: '8px',
                    fontWeight: '600',
                    color: '#1d2327',
                    fontSize: '14px',
                    lineHeight: '1.4'
                }}>
                    {label} {required && <span style={{ color: '#dc3232', marginLeft: '4px' }}>*</span>}
                </label>
            )}
            {children}
            {help && (
                <p style={{
                    fontSize: '13px',
                    color: '#646970',
                    marginTop: '8px',
                    marginBottom: '0',
                    lineHeight: '1.4'
                }}>
                    {help}
                </p>
            )}
        </div>
    );
};

export default FormField;
