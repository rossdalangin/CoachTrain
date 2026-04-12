import React, { useState } from 'react';
import OfferBuilder from './OfferBuilder';

const Clients = () => {
    const [offers, setOffers] = useState([
        { id: 1, title: '90-Day Transformation', price: 2997, type: 'one-time' },
    ]);

    return (
        <div>
            <OfferBuilder />

            <div className="cce-card">
                <h3>Your Active Offers</h3>
                <table className="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {offers.map(offer => (
                            <tr key={offer.id}>
                                <td><strong>{offer.title}</strong></td>
                                <td>${offer.price}</td>
                                <td>{offer.type.toUpperCase()}</td>
                                <td><button className="button button-small">Edit</button></td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default Clients;
