import React from 'react';
import MasterLayout from '../layouts/MasterLayout';

const About = () => {
    return (
        <MasterLayout>
            <div className="container py-5">
                <div className="row justify-content-center">
                    <div className="col-lg-8">
                        <div className="card border-0 shadow-sm rounded-3">
                            <div className="card-body p-4 p-md-5">
                                <h2 className="fw-bold mb-4" style={{ color: '#333' }}>আমাদের সম্পর্কে</h2>
                                <p className="text-muted small mb-4">JHR Bazar — আপনার বিশ্বস্ত অনলাইন শপিং প্ল্যাটফর্ম</p>

                                <div style={{ lineHeight: '1.9', color: '#555' }}>
                                    <h5 className="fw-bold text-dark mt-4">আমরা কারা?</h5>
                                    <p>JHR Bazar একটি বিশ্বস্ত বাংলাদেশি ই-কমার্স প্ল্যাটফর্ম যেখানে আপনি সহজেই আপনার পছন্দের পণ্য অর্ডার করতে পারবেন। আমরা দেশের বিভিন্ন প্রান্তে দ্রুত ও নির্ভরযোগ্য ডেলিভারি সেবা প্রদান করি।</p>

                                    <h5 className="fw-bold text-dark mt-4">আমাদের লক্ষ্য</h5>
                                    <p>আমাদের লক্ষ্য হলো প্রতিটি গ্রাহকের কাছে সেরা মানের পণ্য সাশ্রয়ী মূল্যে পৌঁছে দেওয়া এবং একটি নিরাপদ, সহজ ও আনন্দদায়ক কেনাকাটার অভিজ্ঞতা নিশ্চিত করা।</p>

                                    <h5 className="fw-bold text-dark mt-4">আমাদের সেবাসমূহ</h5>
                                    <ul>
                                        <li>অনলাইন পণ্য কেনাকাটা</li>
                                        <li>দ্রুত ও নিরাপদ ডেলিভারি</li>
                                        <li>সহজ রিটার্ন ও রিফান্ড পলিসি</li>
                                        <li>বিশ্বস্ত বিক্রেতাদের সাথে সংযোগ</li>
                                        <li>ডিজিটাল পণ্য কেনার সুবিধা</li>
                                    </ul>

                                    <h5 className="fw-bold text-dark mt-4">আমাদের সাথে যোগাযোগ করুন</h5>
                                    <p>আপনার যেকোনো প্রশ্ন বা মতামতের জন্য আমাদের সাথে যোগাযোগ করুন। আমরা সবসময় আপনার পাশে আছি।</p>

                                    <div className="mt-5 p-3 bg-light rounded text-center">
                                        <p className="mb-0 small fw-bold">যোগাযোগ: support@jhrbazar.com</p>
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

export default About;
