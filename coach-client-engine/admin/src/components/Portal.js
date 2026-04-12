import React, { useState, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';

const Portal = () => {
    const [resources, setResources] = useState([]);

    useEffect(() => {
        apiFetch({ path: '/cce/v1/portal/resources' }).then(res => {
            if (res.success) setResources(res.data);
        });
    }, []);

    return (
        <div className="cce-card">
            <h3>Shared Coaching Resources</h3>
            <div className="cce-card-grid">
                {resources.map(res => (
                    <div key={res.id} className="cce-card" style={{border: '1px solid #ddd'}}>
                        <h4>{res.title}</h4>
                        <p>{res.type}</p>
                        <button className="button">Manage</button>
                    </div>
                ))}
            </div>
            <button className="button button-primary" style={{marginTop: '20px'}}>Add New Resource</button>
        </div>
    );
};

export default Portal;
