import React, { useState, useEffect } from 'react';
import apiFetch from '@wordpress/api-fetch';

const Bookings = () => {
    const [bookings, setBookings] = useState([]);

    useEffect(() => {
        apiFetch({ path: '/cce/v1/bookings' }).then(res => {
            if (res.success) setBookings(res.data);
        });
    }, []);

    return (
        <div className="cce-card">
            <h3>Scheduled Consultations</h3>
            <table className="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Lead</th>
                        <th>Time</th>
                        <th>Timezone</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    {bookings.map(booking => (
                        <tr key={booking.id}>
                            <td>{booking.lead_name || 'Lead #' + booking.lead_id}</td>
                            <td>{booking.start_time}</td>
                            <td>{booking.timezone}</td>
                            <td><span className={`status-tag status-${booking.status}`}>{booking.status.toUpperCase()}</span></td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
};

export default Bookings;
