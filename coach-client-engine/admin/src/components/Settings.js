import React, { useState, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';

const Settings = ({ setIsPro }) => {
    const [settings, setSettings] = useState({ license_key: '', stripe_api_key: '' });
    const [isSaving, setIsSaving] = useState(false);

    useEffect(() => {
        apiFetch({ path: '/cce/v1/settings' }).then(response => {
            if (response.success) {
                setSettings(response.data);
            }
        });
    }, []);

    const handleSave = () => {
        setIsSaving(true);
        apiFetch({
            path: '/cce/v1/settings',
            method: 'POST',
            data: settings
        }).then(response => {
            if (response.success) {
                setIsPro(settings.license_key.startsWith('PRO-'));
                alert('Settings saved successfully!');
            }
            setIsSaving(false);
        }).catch(err => {
            console.error(err);
            setIsSaving(false);
        });
    };

    return (
        <div className="cce-card">
            <h3>General Settings</h3>
            <div className="form-group" style={{marginBottom: '20px'}}>
                <label style={{display: 'block', marginBottom: '5px'}}>License Key</label>
                <input
                    type="text"
                    className="regular-text"
                    value={settings.license_key}
                    onChange={(e) => setSettings({...settings, license_key: e.target.value})}
                    placeholder="PRO-XXXX-XXXX"
                />
                <p className="description">Enter your license key to unlock Pro features.</p>
            </div>
            <div className="form-group" style={{marginBottom: '20px'}}>
                <label style={{display: 'block', marginBottom: '5px'}}>Stripe API Key</label>
                <input
                    type="password"
                    className="regular-text"
                    value={settings.stripe_api_key}
                    onChange={(e) => setSettings({...settings, stripe_api_key: e.target.value})}
                />
            </div>
            <button
                className="button button-primary"
                onClick={handleSave}
                disabled={isSaving}
            >
                {isSaving ? 'Saving...' : 'Save Settings'}
            </button>
        </div>
    );
};

export default Settings;
