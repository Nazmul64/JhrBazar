import React from 'react';
import MasterLayout from '../layouts/MasterLayout';

const Terms = () => {
    return (
        <MasterLayout>
            <div className="container py-5">
                <div className="row justify-content-center">
                    <div className="col-lg-8">
                        <div className="card border-0 shadow-sm rounded-3">
                            <div className="card-body p-4 p-md-5">
                                <h2 className="fw-bold mb-4" style={{ color: '#333' }}>Terms and Conditions</h2>
                                <p className="text-muted small mb-4">Last Updated: October 2023</p>

                                <div style={{ lineHeight: '1.8', color: '#555' }}>
                                    <h5 className="fw-bold text-dark mt-4">1. Introduction</h5>
                                    <p>Welcome to {settings?.website_name || ''}. By accessing our website and using our services, you agree to comply with and be bound by the following terms and conditions. Please read them carefully.</p>

                                    <h5 className="fw-bold text-dark mt-4">2. Use of the Site</h5>
                                    <p>You must be at least 18 years old to use our services. You agree to use the website only for lawful purposes and in a way that does not infringe the rights of, restrict, or inhibit anyone else's use of the website.</p>

                                    <h5 className="fw-bold text-dark mt-4">3. Product Information and Pricing</h5>
                                    <p>While we strive to provide accurate product and pricing information, pricing or typographical errors may occur. In the event that an item is listed at an incorrect price, we reserve the right to refuse or cancel any orders placed for that item.</p>

                                    <h5 className="fw-bold text-dark mt-4">4. Payment and Billing</h5>
                                    <p>We accept various payment methods. By submitting your payment information, you authorize us to charge the applicable amount to your selected payment method.</p>

                                    <h5 className="fw-bold text-dark mt-4">5. Return and Refund Policy</h5>
                                    <p>We offer a hassle-free return policy. If you are not satisfied with your purchase, please refer to our Return Policy page for instructions on how to return items and receive a refund.</p>

                                    <h5 className="fw-bold text-dark mt-4">6. Changes to Terms</h5>
                                    <p>We reserve the right to update or modify these terms and conditions at any time without prior notice. Your continued use of the website following any changes indicates your acceptance of the new terms.</p>

                                    <div className="mt-5 p-3 bg-light rounded text-center">
                                        <p className="mb-0 small fw-bold">If you have any questions about these Terms, please contact us at {settings?.email_address || ''}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MasterLayout>
    );
};

export default Terms;
