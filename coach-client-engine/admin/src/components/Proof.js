import React, { useState, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';

const Proof = () => {
    const [testimonials, setTestimonials] = useState([]);

    useEffect(() => {
        apiFetch({ path: '/cce/v1/proof/testimonials' }).then(res => {
            if (res.success) setTestimonials(res.data);
        });
    }, []);

    return (
        <div className="cce-card">
            <h3>Testimonials & Case Studies</h3>
            <div className="cce-card-grid">
                {testimonials.map(t => (
                    <div key={t.id} className="cce-card" style={{border: '1px solid #ddd'}}>
                        <p>"{t.content}"</p>
                        <strong>- {t.client}</strong>
                    </div>
                ))}
            </div>
            <button className="button button-primary" style={{marginTop: '20px'}}>Request Testimonial</button>
        </div>
    );
};

export default Proof;
