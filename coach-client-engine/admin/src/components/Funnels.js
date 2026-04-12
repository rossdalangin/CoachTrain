import React, { useState, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';

const Funnels = () => {
    const [funnelSteps, setFunnelSteps] = useState([]);
    const [isSaving, setIsSaving] = useState(false);

    useEffect(() => {
        apiFetch({ path: '/cce/v1/funnels/1/steps' }).then(res => {
            if (res.success) setFunnelSteps(res.data);
        });
    }, []);

    const addStep = () => {
        const newStep = { id: Date.now(), title: 'New Step', type: 'optin' };
        setFunnelSteps([...funnelSteps, newStep]);
    };

    const removeStep = (id) => {
        setFunnelSteps(funnelSteps.filter(step => step.id !== id));
    };

    const moveStep = (index, direction) => {
        const newSteps = [...funnelSteps];
        const element = newSteps.splice(index, 1)[0];
        newSteps.splice(index + direction, 0, element);
        setFunnelSteps(newSteps);
    };

    const handleSave = () => {
        setIsSaving(true);
        apiFetch({
            path: '/cce/v1/funnels/1/steps',
            method: 'POST',
            data: { steps: funnelSteps }
        }).then(() => {
            alert('Funnel steps saved!');
            setIsSaving(false);
        });
    };

    return (
        <div>
            <div className="cce-card" style={{marginBottom: '20px'}}>
                <h3>Your Funnel Flow</h3>
                <div className="funnel-steps" style={{display: 'flex', flexDirection: 'column', gap: '10px'}}>
                    {funnelSteps.map((step, index) => (
                        <div key={step.id} className="funnel-step-card" style={{
                            background: '#fff',
                            border: '1px solid #ddd',
                            padding: '15px',
                            borderRadius: '4px',
                            display: 'flex',
                            justifyContent: 'space-between',
                            alignItems: 'center'
                        }}>
                            <div style={{display: 'flex', alignItems: 'center'}}>
                                <div style={{display: 'flex', flexDirection: 'column', marginRight: '15px'}}>
                                    <button disabled={index === 0} onClick={() => moveStep(index, -1)} style={{padding:0, cursor: 'pointer', border:'none', background:'none'}}>▲</button>
                                    <button disabled={index === funnelSteps.length - 1} onClick={() => moveStep(index, 1)} style={{padding:0, cursor: 'pointer', border:'none', background:'none'}}>▼</button>
                                </div>
                                <span style={{fontWeight: 'bold', marginRight: '10px'}}>#{index + 1}</span>
                                {step.title} <span style={{fontSize: '12px', color: '#666'}}>({step.step_type || step.type})</span>
                            </div>
                            <button className="button" onClick={() => removeStep(step.id)}>Remove</button>
                        </div>
                    ))}
                </div>
                <div style={{marginTop: '20px', display: 'flex', gap: '10px'}}>
                    <button className="button button-primary" onClick={addStep}>+ Add Step</button>
                    <button className="button" onClick={handleSave} disabled={isSaving}>{isSaving ? 'Saving...' : 'Save Flow'}</button>
                </div>
            </div>
        </div>
    );
};

export default Funnels;
