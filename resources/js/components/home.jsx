import React, {useEffect, useState} from 'react';
import Links from "./links.jsx";

const Home = ({setPage, getToken}) => {

    const [formData, setFormData] = useState({
        url: '',
        use_js_redirect: false
    });

    let token = getToken();

    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    const handleChange = (e) => {
        const { name, value, files } = e.target;
        setFormData({ ...formData, [name]: value });
    };

    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsSubmitting(true);
        setSuccess('')

        const data = new FormData();
        data.append('url', formData.url);
        data.append('use_js_redirect', formData.use_js_redirect  ? "1" : "0");


        try {
            const response = await fetch('/api/short-links', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
                body: data,
            });

            if (response.ok) {
                const result = await response.text();
                let link = JSON.parse(result).short_link
                setSuccess(<p>Link Created: <a href={link}>{link}</a></p>)
                setError('');
            } else if (response.status === 429) {
                setError('Too many requests. Please try again later.');
            } else {
                const error = await response.json();
                setError('Can\'t create link: ' + Object.values(error.errors)
                    .flatMap(messages => messages)
                    .map(message => `"${message}"`));
                console.error('Error:', error);
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            setIsSubmitting(false);
        }
    };

    const handleCheckboxChange = (e) => {
        setFormData({ ...formData, ['use_js_redirect']: e.target.checked });
    };


    return (
        <div>
            <h1 className={"text-center"}>Create short link</h1>
            <br/>
            {error && <div className="alert alert-danger">{error}</div>}
            {success && <div className="alert alert-success">{success}</div>}
            <form onSubmit={handleSubmit}>
                <div className="form-group">
                    <label htmlFor="url">Original url</label>
                    <input type="text" className="form-control" id="url" aria-describedby="url" onChange={handleChange}
                           placeholder="Enter url" name="url" disabled={isSubmitting} value={formData.url}/>
                </div>
                <div className="form-check">
                    <input className="form-check-input" type="checkbox" id="use_js_redirect"
                           checked={formData.checkbox} onChange={handleCheckboxChange}/>
                    <label className="form-check-label" htmlFor="use_js_redirect">
                        Use js redirect (slower, but can collect additional info)
                    </label>
                </div>

                <button type="submit" className="btn btn-primary mt-2">Create</button>
            </form>

            <Links getToken={getToken}/>
        </div>
    );
};

export default Home;
