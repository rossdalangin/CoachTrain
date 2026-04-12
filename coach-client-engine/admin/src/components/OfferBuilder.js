import React, { useState } from 'react';
import apiFetch from '@wordpress/api-fetch';

const OfferBuilder = () => {
    const [offer, setOffer] = useState({
        title: '',
        price: '',
        dream_outcome: '',
        likelihood: '',
        time_delay: '',
        effort: ''
    });
    const [isSaving, setIsSaving] = useState(false);

    const handleSave = () => {
        setIsSaving(true);
        const description = `Outcome: ${offer.dream_outcome} | Likelihood: ${offer.likelihood} | Time: ${offer.time_delay} | Effort: ${offer.effort}`;

        apiFetch({
            path: '/cce/v1/offers',
            method: 'POST',
            data: {
                title: offer.title,
                price: offer.price,
                description: description
            }
        }).then(() => {
            alert('Grand Slam Offer saved!');
            setIsSaving(false);
        });
    };

    return (
        <div className="cce-card" style={{borderLeft: '4px solid #ff4136'}}>
            <h3>💎 Alex Hormozi "Grand Slam" Offer Builder</h3>
            <p>Use the Value Equation to create an irresistible offer.</p>
            <div className="form-group" style={{marginBottom: '15px'}}>
                <label>Offer Title</label>
                <input
                    type="text"
                    className="large-text"
                    value={offer.title}
                    onChange={(e) => setOffer({...offer, title: e.target.value})}
                    placeholder="e.g. 90-Day Business Accelerator"
                    style={{width: '100%'}}
                />
            </div>
            <div className="form-group" style={{marginBottom: '15px'}}>
                <label>Price ($)</label>
                <input
                    type="number"
                    className="large-text"
                    value={offer.price}
                    onChange={(e) => setOffer({...offer, price: e.target.value})}
                    placeholder="5000"
                    style={{width: '100%'}}
                />
            </div>
            <div className="value-equation-grid" style={{display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '20px'}}>
                <div className="field">
                    <label>Dream Outcome</label>
                    <input type="text" style={{width: '100%'}} value={offer.dream_outcome} onChange={(e) => setOffer({...offer, dream_outcome: e.target.value})} placeholder="The big result..." />
                </div>
                <div className="field">
                    <label>Perceived Likelihood</label>
                    <input type="text" style={{width: '100%'}} value={offer.likelihood} onChange={(e) => setOffer({...offer, likelihood: e.target.value})} placeholder="Guarantee/Proof..." />
                </div>
                <div className="field">
                    <label>Time Delay</label>
                    <input type="text" style={{width: '100%'}} value={offer.time_delay} onChange={(e) => setOffer({...offer, time_delay: e.target.value})} placeholder="How fast?" />
                </div>
                <div className="field">
                    <label>Effort & Sacrifice</label>
                    <input type="text" style={{width: '100%'}} value={offer.effort} onChange={(e) => setOffer({...offer, effort: e.target.value})} placeholder="What do they give up?" />
                </div>
            </div>
            <button className="button button-primary" style={{marginTop: '20px'}} onClick={handleSave} disabled={isSaving}>
                {isSaving ? 'Saving...' : 'Save Grand Slam Offer'}
            </button>
        </div>
    );
};

export default OfferBuilder;
