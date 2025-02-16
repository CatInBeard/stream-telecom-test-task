import React, { useEffect, useState } from 'react';

const ShortLinksList = ({ getToken }) => {
    const [shortLinks, setShortLinks] = useState([]);
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(true);
    const [currentPage, setCurrentPage] = useState(1);
    const [perPage, setPerPage] = useState(100);
    const [totalPages, setTotalPages] = useState(0);

    const fetchShortLinks = async (page) => {
        try {
            const token = getToken();
            const response = await fetch(`/api/short-links?page=${page}&per_page=${perPage}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
            });

            if (!response.ok) {
                const errorData = await response.json();
                console.error(errorData)
            }

            const data = await response.text();
            const parsedData = JSON.parse(data);

            console.log(parsedData)

            setShortLinks(parsedData.data);
            setTotalPages(Math.ceil(parsedData.total / perPage));
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchShortLinks(currentPage);
    }, [currentPage, perPage]);

    const handleNextPage = () => {
        if (currentPage < totalPages) {
            setCurrentPage((prevPage) => prevPage + 1);
        }
    };

    const handlePrevPage = () => {
        if (currentPage > 1) {
            setCurrentPage((prevPage) => prevPage - 1);
        }
    };

    if (loading) {
        return <div>Loading...</div>;
    }

    if (error) {
        return <div>Error: {error}</div>;
    }

    return (
        <div>
            <hr className={"m-5"} />
            <div>
                <button className={"btn btn-secondary m-2"} onClick={handlePrevPage} disabled={currentPage === 1}>
                    Previous
                </button>
                <button className={"btn btn-secondary m-2"} onClick={handleNextPage} disabled={currentPage === totalPages}>
                    Next
                </button>
            </div>
            <p>Page {currentPage} of {totalPages}</p>
            <h2>Short links: </h2>
            <ul>
                {shortLinks.map(link => (
                    <li key={link.id}>
                        <div className={"card border m-1 p-3"}>
                            Short: <a href={link.short_link} target="_blank">
                                {link.short_link}
                        </a> <br/>
                            Original: <a href={link.link} target="_blank">{link.link}</a> <br/>
                            <button className={"btn btn-primary mt-3"} style={{ 'max-width' : '200px'}}> Open statistics </button>
                        </div>
                    </li>
                ))}
            </ul>
            <div>
                <button className={"btn btn-secondary m-2"} onClick={handlePrevPage} disabled={currentPage === 1}>
                    Previous
                </button>
                <button className={"btn btn-secondary m-2"} onClick={handleNextPage} disabled={currentPage === totalPages}>
                    Next
                </button>
            </div>
            <p>Page {currentPage} of {totalPages}</p>
        </div>
    );
};

export default ShortLinksList;
