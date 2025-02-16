import React, {useState} from 'react';

const Welcome = ({setPage, getToken}) => {

    const [formData, setFormData] = useState({
        email: '',
        name: '',
        password: '',
        passwordConfirm: '',
    });

    let token = getToken();

    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    const handleChange = (e) => {
        const { name, value, files } = e.target;
        setFormData({ ...formData, [name]: value });
    };

    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleLogin = async => {
        setPage('login')
    }

    const handleGoHome = async => {
        setPage('home')
    }

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsSubmitting(true);
        setSuccess('')

        if (formData.password !== formData.passwordConfirm) {
            setError('Password does not match');
            setIsSubmitting(false);
            return;
        }

        const data = new FormData();
        data.append('name', formData.name);
        data.append('email', formData.email);
        data.append('password', formData.password);

        try {
            const response = await fetch('/api/user', {
                method: 'POST',
                body: data,
            });

            if (response.ok) {
                const result = await response.text();
                console.log('Success:', result);
                setSuccess(<p>User created, please wait email with confirmation <a href={"http://localhost:8025"}>go to mailpit</a></p>)
                setError('');
            } else if (response.status === 429) {
                setError('Too many requests. Please try again later.');
            } else {
                const error = await response.json();
                setError('Can\'t create user' + error);
                console.error('Error:', error);
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <div>
            <div>
                {!token && <div className={"btn btn-primary float-end"} onClick={handleLogin}>Login</div>}
                {token && <div className={"btn btn-success float-end"} onClick={handleGoHome}>You are logged in, go home</div>}
            </div>
            <h1 className={"text-center"}>Welcome to test task!</h1>
            <p>You can open openapi doc: <a href={"/docs"}>here</a></p>
            <br/>
            <p>Create an account:</p>
            {error && <div className="alert alert-danger">{error}</div>}
            {success && <div className="alert alert-success">{success}</div>}
            <form onSubmit={handleSubmit} >
                <div className="form-group">
                    <label htmlFor="email">Email address</label>
                    <input type="email" className="form-control" id="email" aria-describedby="emailHelp" onChange={handleChange}
                           placeholder="Enter email" required={true} name="email" disabled={isSubmitting}/>
                    <small id="emailHelp" className="form-text text-muted">We'll never share your email with anyone
                        else.</small>
                </div>
                <div className="form-group">
                    <label htmlFor="name">Name</label>
                    <input type="text" className="form-control" id="name" onChange={handleChange}
                           placeholder="Enter name" required={true} name="name" disabled={isSubmitting}/>
                </div>
                <div className="form-group">
                    <label htmlFor="password">Password</label>
                    <input type="password" className="form-control" id="password" placeholder="Password" onChange={handleChange}
                           required={true} name="password" disabled={isSubmitting}/>
                </div>
                <div className="form-group">
                    <label htmlFor="passwordConfirm">Password confirm</label>
                    <input type="password" className="form-control" id="passwordConfirm" placeholder="Password" onChange={handleChange}
                           required={true} name="passwordConfirm" disabled={isSubmitting}/>
                </div>


                <button type="submit" className="btn btn-primary">Submit</button>
            </form>
        </div>
    );
};

export default Welcome;
