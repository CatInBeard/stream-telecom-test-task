import React, { useEffect, useState } from 'react';

import s from "./PopupContainer.module.css";

const StatisticsModal = ({ onClose, linkId, getToken }) => {
    const [statistics, setStatistics] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchStatistics = async () => {
            if (!linkId) return;

            try {
                const token = getToken();
                const response = await fetch(`/api/short-links/${linkId}/additional_info`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                    },
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    console.error(errorData.error)
                }

                const data = await response.text();
                const parsedData = JSON.parse(data);

                setStatistics(parsedData.data);
            } catch (err) {
                setError(err.message);
            } finally {
                setLoading(false);
            }
        };

        fetchStatistics();
    }, [linkId, getToken]);

    const renderValue = (value) => {
        if (Array.isArray(value)) {
            return (
                <ul>
                    {value.map((item, index) => (
                        <li key={index}>{renderValue(item)}</li>
                    ))}
                </ul>
            );
        } else if (typeof value === 'object' && value !== null) {
            return (
                <ul>
                    {Object.entries(value).map(([key, val]) => (
                        <li key={key}>
                            <strong>{key}:</strong> {renderValue(val)}
                        </li>
                    ))}
                </ul>
            );
        } else {
            return value?.toString() || 'N/A';
        }
    };


    return (
        <>
            <div className={"position-fixed card top-50 start-50 translate-middle p-3 " + s.Front}>
                {loading && <div>Loading statistics...</div>}
                {error && <div>Error: {error}</div>}
                {!loading && !error && (
                    <div>
                        <h2>Statistics: <span className="float-end btn" onClick={onClose}>X</span></h2>
                        <ul>
                            {statistics.map(stat => (
                                <li key={stat.id}>
                                    {Object.entries(stat).map(([key, value]) => (
                                        <div key={key}>
                                            <strong>{key}:</strong> {renderValue(value)} <br />
                                        </div>
                                    ))}
                                    <hr className={"m-1"}/>
                                </li>
                            ))}
                        </ul>
                    </div>
                )}
            </div>
            <div className={"position-fixed top-0 start-0 " + s.Background}>
            </div>
        </>
    );
};

export default StatisticsModal;
