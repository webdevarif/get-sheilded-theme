import React from 'react';
import Select from 'react-select';

// Custom color theme for react-select
const customTheme = (theme) => ({
    ...theme,
    colors: {
        ...theme.colors,
        primary: '#2271b1',        // WordPress blue
        primary75: '#2271b1',
        primary50: '#2271b1',
        primary25: '#f0f6fc',      // Light blue background
        danger: '#dc3232',         // WordPress red
        dangerLight: '#fbeaea',    // Light red background
        neutral0: '#fff',           // White background
        neutral5: '#f6f7f7',      // Light gray
        neutral10: '#e0e0e0',     // Gray
        neutral20: '#c3c4c7',     // Border gray
        neutral30: '#8c8f94',     // Text gray
        neutral40: '#646970',     // Darker text gray
        neutral50: '#1d2327',      // Dark text
        neutral60: '#1d2327',
        neutral70: '#1d2327',
        neutral80: '#1d2327',
        neutral90: '#1d2327',
    },
});

// Custom styles for react-select
const customStyles = {
    control: (provided, state) => ({
        ...provided,
        minHeight: '36px',
        border: `1px solid ${state.isFocused ? '#2271b1' : '#8c8f94'}`,
        borderRadius: '4px',
        boxShadow: state.isFocused ? '0 0 0 1px #2271b1' : 'none',
        fontSize: '14px',
        padding: '0 8px',
        '&:hover': {
            borderColor: '#2271b1',
        },
    }),
    valueContainer: (provided) => ({
        ...provided,
        padding: '0',
    }),
    inputContainer: (provided) => ({
        ...provided,
        margin: '0',
        padding: '0',
    }),
    placeholder: (provided) => ({
        ...provided,
        color: '#8c8f94',
        fontSize: '14px',
    }),
    singleValue: (provided) => ({
        ...provided,
        color: '#1d2327',
        fontSize: '14px',
    }),
    multiValue: (provided) => ({
        ...provided,
        backgroundColor: '#e0e0e0',
        borderRadius: '3px',
        margin: '2px',
    }),
    multiValueLabel: (provided) => ({
        ...provided,
        color: '#32373c',
        fontSize: '13px',
        padding: '4px 8px',
    }),
    multiValueRemove: (provided, state) => ({
        ...provided,
        color: '#32373c',
        '&:hover': {
            backgroundColor: '#dc3232',
            color: 'white',
        },
    }),
    menu: (provided) => ({
        ...provided,
        border: '1px solid #c3c4c7',
        borderRadius: '4px',
        boxShadow: '0 1px 1px rgba(0,0,0,.04)',
        fontSize: '14px',
    }),
    option: (provided, state) => ({
        ...provided,
        fontSize: '14px',
        padding: '8px 12px',
        backgroundColor: state.isFocused ? '#f0f0f0' : 'transparent',
        color: state.isSelected ? '#1e1e1e' : '#1d2327',
        fontWeight: state.isSelected ? '600' : 'normal',
        '&:hover': {
            backgroundColor: '#f0f0f0',
            color: '#1e1e1e',
        },
    }),
    indicatorSeparator: (provided) => ({
        ...provided,
        backgroundColor: '#8c8f94',
    }),
    dropdownIndicator: (provided) => ({
        ...provided,
        color: '#8c8f94',
    }),
    clearIndicator: (provided) => ({
        ...provided,
        color: '#8c8f94',
    }),
};

const CustomSelect = ({
    value,
    onChange,
    options = [],
    placeholder = 'Select...',
    isMulti = false,
    isClearable = true,
    isSearchable = true,
    isLoading = false,
    isDisabled = false,
    className = '',
    classNamePrefix = 'custom-select',
    ...props
}) => {
    return (
        <Select
            value={value}
            onChange={onChange}
            options={options}
            placeholder={placeholder}
            isMulti={isMulti}
            isClearable={isClearable}
            isSearchable={isSearchable}
            isLoading={isLoading}
            isDisabled={isDisabled}
            className={className}
            classNamePrefix={classNamePrefix}
            theme={customTheme}
            styles={customStyles}
            {...props}
        />
    );
};

export default CustomSelect;
