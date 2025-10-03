import React from 'react';
import Select from 'react-select';

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
    const customStyles = {
        control: (provided, state) => ({
            ...provided,
            minHeight: '36px',
            border: state.isFocused ? '1px solid #2271b1' : '1px solid #8c8f94',
            boxShadow: state.isFocused ? '0 0 0 1px #2271b1' : 'none',
            borderRadius: '4px',
            fontSize: '14px',
            padding: '0 8px',
            backgroundColor: '#fff',
            '&:hover': {
                borderColor: '#2271b1',
            },
        }),
        valueContainer: (provided) => ({
            ...provided,
            padding: '0',
        }),
        input: (provided) => ({
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
        multiValueRemove: (provided) => ({
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
            backgroundColor: state.isFocused ? '#f0f0f0' : 'white',
            color: '#1e1e1e',
            '&:active': {
                backgroundColor: '#e0e0e0',
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
            styles={customStyles}
            {...props}
        />
    );
};

export default CustomSelect;
