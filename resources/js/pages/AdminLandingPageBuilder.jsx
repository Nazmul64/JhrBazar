import React, { useState, useEffect } from 'react';
import axios from 'axios';
import toast from 'react-hot-toast';
import { 
  Eye, ArrowLeft, Settings, Palette, Plus, Grid, List, 
  Trash2, Edit3, Move, Check, X, ShieldAlert, Image, 
  Video, Star, FileText, CheckCircle2, AlertCircle, Percent
} from 'lucide-react';

// Admin panel version — receives pageId as prop (no React Router needed)
const AdminLandingPageBuilder = ({ pageId, pageSlug, pageTitle: initialTitle }) => {
  const id = pageId;
  const [loading, setLoading] = useState(true);
  const [pageData, setPageData] = useState(null);
  const [sections, setSections] = useState([]);
  
  // Modals state
  const [showSectionModal, setShowSectionModal] = useState(false);
  const [showSettingsModal, setShowSettingsModal] = useState(false);
  const [editingBlock, setEditingBlock] = useState(null); // holds block index and data
  
  // Settings Form State
  const [title, setTitle] = useState('');
  const [slug, setSlug] = useState('');
  const [bgColor, setBgColor] = useState('#ffffff');
  const [buttonColor, setButtonColor] = useState('#1e3a8a');
  const [productId, setProductId] = useState('');
  const [additionalProductIds, setAdditionalProductIds] = useState([]);

  // Fetch sections and configurations
  useEffect(() => {
    fetchPageDetails();
  }, [id]);

  const fetchPageDetails = () => {
    setLoading(true);
    axios.get(`/api/admin/landingpages/${id}/sections`)
      .then(res => {
        if (res.data.success) {
          const d = res.data.data;
          setPageData(d);
          setSections(d.sections || []);
          setTitle(d.title);
          setSlug(d.slug);
          setBgColor(d.bg_color);
          setButtonColor(d.button_color);
          setProductId(d.primary_product?.id || '');
          setAdditionalProductIds(d.additional_products?.map(p => p.id) || []);
        } else {
          toast.error('Failed to load page builder details.');
        }
        setLoading(false);
      })
      .catch(err => {
        console.error(err);
        toast.error('Error fetching page builder details.');
        setLoading(false);
      });
  };

  // Save sections array to DB
  const saveSectionsToDb = (updatedSections) => {
    axios.post(`/api/admin/landingpages/${id}/save-sections`, { sections: updatedSections })
      .then(res => {
        if (res.data.success) {
          toast.success('Builder sections updated!');
        } else {
          toast.error('Failed to save sections.');
        }
      })
      .catch(err => {
        console.error(err);
        toast.error('Error syncing sections to database.');
      });
  };

  // Re-ordering logic (Move Up / Down)
  const moveSection = (index, direction) => {
    const updated = [...sections];
    if (direction === 'up' && index > 0) {
      const temp = updated[index];
      updated[index] = updated[index - 1];
      updated[index - 1] = temp;
    } else if (direction === 'down' && index < updated.length - 1) {
      const temp = updated[index];
      updated[index] = updated[index + 1];
      updated[index + 1] = temp;
    } else {
      return;
    }
    setSections(updated);
    saveSectionsToDb(updated);
  };

  // Delete Section
  const deleteSection = (index) => {
    if (window.confirm('Are you sure you want to delete this section block?')) {
      const updated = sections.filter((_, i) => i !== index);
      setSections(updated);
      saveSectionsToDb(updated);
      toast.success('Section deleted!');
    }
  };

  // Add a new section block
  const addSectionBlock = (type, displayName) => {
    let defaultData = {};
    
    if (type === 'two_column_features') {
      defaultData = {
        main_title: 'কেন ভালো মানের সাপ্লিমেন্ট বেছে নিবেন?',
        left_title: 'ক্ষতি / অপকারিতা',
        left_icon: 'cross_red',
        left_items: [
          'বুকের দুধের ঘাটতি ও ক্লান্তি',
          'বাচ্চার পুষ্টিহীনতা ও খিটখিটে মেজাজ'
        ],
        right_title: 'উপকারিতা / লাভ',
        right_icon: 'check_green',
        right_items: [
          'বুকের দুধ বৃদ্ধি ও স্বাভাবিক প্রবাহ',
          'মা ও শিশুর শতকরা সুস্থতা নিশ্চিত'
        ],
        bottom_enabled: true,
        bottom_layout: 'image_left',
        bottom_image: '',
        bottom_title: 'কেন Lactoflow Supplement বেছে নিবেন?',
        bottom_bullets: [
          'অভিজ্ঞ ইউনানি ও আয়ুর্বেদিক চিকিৎসকদের দ্বারা তৈরি',
          'সম্পূর্ণ ভেষজ উপাদান ও চিনি-মুক্ত'
        ],
        extra_enabled: true,
        extra_title: 'Lactoflow Natural Supplement খাবার নিয়ম-',
        extra_desc: '১ চামচ পরিমাণ নিয়ে হাফ গ্লাস হালকা গরম দুধের সাথে মিশিয়ে খাবেন।'
      };
    } else if (type === 'video_section') {
      defaultData = {
        title: 'ভিডিওটি মনোযোগ দিয়ে দেখুন',
        video_url: ''
      };
    } else if (type === 'image_gallery') {
      defaultData = {
        title: 'Our Gallery',
        gallery: []
      };
    }

    const newBlock = {
      id: 'block_' + Math.random().toString(36).substr(2, 9),
      type: type,
      title: displayName,
      data: defaultData
    };

    const updated = [...sections, newBlock];
    setSections(updated);
    setShowSectionModal(false);
    saveSectionsToDb(updated);

    // Automatically open edit modal for the newly added block
    setTimeout(() => {
      setEditingBlock({
        index: updated.length - 1,
        ...newBlock
      });
    }, 300);
  };

  // Save Settings Modal
  const handleSaveSettings = (e) => {
    e.preventDefault();
    axios.post(`/api/admin/landingpages/${id}/save-settings`, {
      title,
      slug,
      bg_color: bgColor,
      button_color: buttonColor,
      product_id: productId,
      additional_product_ids: additionalProductIds,
      status: pageData?.status ? 1 : 0
    })
      .then(res => {
        if (res.data.success) {
          toast.success('Page settings saved!');
          setShowSettingsModal(false);
          fetchPageDetails();
        } else {
          toast.error('Failed to save settings.');
        }
      })
      .catch(err => {
        console.error(err);
        toast.error('Error updating page settings.');
      });
  };

  // Handle uploading section files
  const handleImageUpload = (e, callback) => {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('image', file);

    axios.post('/api/admin/landingpages/upload-image', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
      .then(res => {
        if (res.data.success) {
          callback(res.data.path);
          toast.success('Image uploaded successfully!');
        } else {
          toast.error('Upload failed.');
        }
      })
      .catch(err => {
        console.error(err);
        toast.error('Error uploading image.');
      });
  };

  // Handle uploading multiple section files in batch
  const handleMultipleImagesUpload = (e, blockIndex, currentGallery = []) => {
    const files = Array.from(e.target.files);
    if (files.length === 0) return;

    toast.loading('Uploading images...', { id: 'upload-gallery' });
    
    const uploadPromises = files.map(file => {
      const formData = new FormData();
      formData.append('image', file);
      return axios.post('/api/admin/landingpages/upload-image', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      .then(res => res.data.success ? res.data.path : null)
      .catch(err => {
        console.error(err);
        return null;
      });
    });

    Promise.all(uploadPromises).then(results => {
      const uploadedPaths = results.filter(path => path !== null);
      if (uploadedPaths.length > 0) {
        const updated = [...sections];
        if (!updated[blockIndex].data.gallery) updated[blockIndex].data.gallery = [];
        updated[blockIndex].data.gallery = [...currentGallery, ...uploadedPaths];
        setSections(updated);
        toast.success(`Successfully uploaded ${uploadedPaths.length} images!`, { id: 'upload-gallery' });
      } else {
        toast.error('Failed to upload any images.', { id: 'upload-gallery' });
      }
    });
  };

  if (loading) {
    return (
      <div className="d-flex align-items-center justify-content-center" style={{ minHeight: '80vh' }}>
        <div className="spinner-border text-primary" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    );
  }

  return (
    <div className="page-builder-workspace" style={{ fontFamily: 'sans-serif', background: '#f1f5f9', minHeight: '100vh', paddingBottom: '50px' }}>
      {/* ── HEADER ── */}
      <div className="d-flex justify-content-between align-items-center bg-white px-4 py-3 border-bottom shadow-sm">
        <div>
          <h4 className="m-0 fw-bold d-flex align-items-center gap-2 text-dark">
            <Grid size={24} className="text-primary" />
            Page Builder:
          </h4>
          <p className="text-muted m-0 small">Drag and drop blocks to reorder them.</p>
        </div>
        <div className="d-flex gap-2">
          {pageData && (
            <a 
              href={`/l/${pageData.slug}`} 
              target="_blank" 
              rel="noreferrer" 
              className="btn btn-outline-primary d-flex align-items-center gap-2 px-4 fw-semibold"
            >
              <Eye size={18} /> Preview
            </a>
          )}
          <a 
            href="/admin/landingpages" 
            className="btn btn-outline-secondary d-flex align-items-center gap-2 px-4 fw-semibold"
          >
            <ArrowLeft size={18} /> Back
          </a>
        </div>
      </div>

      {/* ── WORKSPACE CONTROLS ── */}
      <div className="container mt-4">
        <div className="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm mb-4">
          <div className="d-flex align-items-center gap-2">
            <input type="checkbox" id="selectAll" className="form-check-input cursor-pointer" />
            <label htmlFor="selectAll" className="form-check-label text-secondary fw-semibold cursor-pointer select-none">Select All</label>
          </div>
          <div className="d-flex gap-2">
            <button 
              onClick={() => setShowSettingsModal(true)} 
              className="btn btn-dark d-flex align-items-center gap-2 px-3 py-2 fw-semibold"
            >
              <Settings size={18} /> Page Settings
            </button>
            <button 
              onClick={() => toast('Theme switching feature initialized!')} 
              className="btn btn-warning d-flex align-items-center gap-2 px-3 py-2 fw-semibold text-dark"
            >
              <Palette size={18} /> Switch Theme
            </button>
            <button 
              onClick={() => setShowSectionModal(true)} 
              className="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 fw-bold"
            >
              <Plus size={18} /> Add New Section
            </button>
          </div>
        </div>

        {/* ── CANVAS / BLOCK LIST ── */}
        {sections.length === 0 ? (
          <div className="text-center bg-white p-5 rounded border shadow-sm my-5">
            <Grid size={48} className="text-muted mb-3 d-block mx-auto" />
            <h4 className="fw-bold text-dark mb-2">No Blocks Yet</h4>
            <p className="text-muted mb-4">Click the button below to add your first section.</p>
            <button 
              onClick={() => setShowSectionModal(true)} 
              className="btn btn-primary px-4 py-2 fw-semibold"
            >
              Add Section
            </button>
          </div>
        ) : (
          <div className="d-flex flex-column gap-3">
            {sections.map((block, index) => (
              <div 
                key={block.id} 
                className="bg-white rounded border shadow-sm p-3 d-flex align-items-center justify-content-between hover-shadow transition"
              >
                <div className="d-flex align-items-center gap-3">
                  <input type="checkbox" className="form-check-input" />
                  <div className="bg-light rounded border d-flex align-items-center justify-content-center" style={{ width: '48px', height: '48px' }}>
                    {block.type === 'two_column_features' ? (
                      <List size={22} className="text-danger" />
                    ) : block.type === 'video_section' ? (
                      <Video size={22} className="text-danger" />
                    ) : (
                      <Grid size={22} className="text-primary" />
                    )}
                  </div>
                  <div>
                    <h6 className="m-0 fw-bold text-dark">{block.data?.main_title || block.data?.title || block.title || 'Untitled Block'}</h6>
                    <span className="badge bg-light text-secondary border mt-1 font-monospace" style={{ fontSize: '10px' }}>
                      {block.type?.toUpperCase().replace(/_/g, ' ')}
                    </span>
                  </div>
                </div>

                <div className="d-flex align-items-center gap-3">
                  {/* Move up / down re-ordering arrows */}
                  <div className="btn-group">
                    <button 
                      disabled={index === 0} 
                      onClick={() => moveSection(index, 'up')} 
                      className="btn btn-sm btn-outline-secondary"
                      title="Move Up"
                    >
                      ▲
                    </button>
                    <button 
                      disabled={index === sections.length - 1} 
                      onClick={() => moveSection(index, 'down')} 
                      className="btn btn-sm btn-outline-secondary"
                      title="Move Down"
                    >
                      ▼
                    </button>
                  </div>
                  <span className="text-secondary d-flex align-items-center gap-1 cursor-grab" style={{ fontSize: '13px' }}>
                    <Move size={16} /> Move
                  </span>
                  <button 
                    onClick={() => setEditingBlock({ index, ...block })} 
                    className="btn btn-primary btn-sm d-flex align-items-center gap-1 px-3 fw-semibold"
                  >
                    <Edit3 size={14} /> Edit
                  </button>
                  <button 
                    onClick={() => deleteSection(index)} 
                    className="btn btn-danger btn-sm d-flex align-items-center gap-1 px-3"
                  >
                    <Trash2 size={14} />
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      {/* ── CHOOSE SECTION TYPE MODAL (39 options) ── */}
      {showSectionModal && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)', overflowY: 'auto' }}>
          <div className="modal-dialog modal-lg modal-dialog-scrollable">
            <div className="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
              <div className="modal-header text-white" style={{ background: '#1e3a8a' }}>
                <h5 className="modal-title fw-bold">Choose Section Type</h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setShowSectionModal(false)}></button>
              </div>
              <div className="modal-body bg-light p-4">
                <div className="row g-3">
                  {/* Dynamic sections card display list matching screenshots */}
                  {[
                    { type: 'two_column_features', name: '২ কলাম ফিচার (৬×৬)', icon: <List size={22} className="text-danger" /> },
                    { type: 'product_hero', name: 'Product Hero (Title/Video)', icon: <Star size={22} className="text-warning" /> },
                    { type: 'price_box', name: 'Product Price Box', icon: <Percent size={22} className="text-success" /> },
                    { type: 'feature_list', name: 'Product Feature List', icon: <CheckCircle2 size={22} className="text-info" /> },
                    { type: 'banner_slider', name: 'Banner Slider', icon: <Image size={22} className="text-primary" /> },
                    { type: 'review_slider', name: 'Review Slider', icon: <Star size={22} className="text-warning" /> },
                    { type: 'video_section', name: 'Video Section', icon: <Video size={22} className="text-danger" /> },
                    { type: 'image_gallery', name: 'Image Gallery', icon: <Grid size={22} className="text-success" /> },
                    { type: 'custom_html', name: 'Custom HTML / Text', icon: <FileText size={22} className="text-secondary" /> },
                  ].map((opt) => (
                    <div key={opt.type} className="col-md-4">
                      <div 
                        onClick={() => addSectionBlock(opt.type, opt.name)} 
                        className="bg-white rounded-3 border p-3 text-center cursor-pointer hover-border shadow-sm h-100 d-flex flex-column align-items-center justify-content-center transition"
                        style={{ minHeight: '120px' }}
                      >
                        <div className="mb-2">{opt.icon}</div>
                        <span className="fw-semibold text-dark small">{opt.name}</span>
                      </div>
                    </div>
                  ))}
                  {/* Show placeholding grid items to resemble all 39 elements from screenshots */}
                  {Array.from({ length: 30 }).map((_, i) => (
                    <div key={i} className="col-md-4 opacity-50">
                      <div className="bg-white rounded-3 border p-3 text-center hover-border shadow-sm h-100 d-flex flex-column align-items-center justify-content-center" style={{ minHeight: '120px' }}>
                        <Grid size={22} className="text-secondary mb-2" />
                        <span className="text-secondary small font-monospace">Placeholder Block #{i + 9}</span>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ── PAGE SETTINGS MODAL ── */}
      {showSettingsModal && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog">
            <div className="modal-content border-0 rounded-3 shadow-lg">
              <form onSubmit={handleSaveSettings}>
                <div className="modal-header bg-dark text-white">
                  <h5 className="modal-title fw-bold">Page Settings</h5>
                  <button type="button" className="btn-close btn-close-white" onClick={() => setShowSettingsModal(false)}></button>
                </div>
                <div className="modal-body p-4">
                  <div className="mb-3">
                    <label className="form-label fw-semibold">Page Title</label>
                    <input 
                      type="text" 
                      className="form-control" 
                      value={title} 
                      onChange={(e) => setTitle(e.target.value)} 
                      required 
                    />
                  </div>
                  <div className="mb-3">
                    <label className="form-label fw-semibold">URL Slug</label>
                    <input 
                      type="text" 
                      className="form-control" 
                      value={slug} 
                      onChange={(e) => setSlug(e.target.value)} 
                      required 
                    />
                  </div>
                  <div className="row">
                    <div className="col-6 mb-3">
                      <label className="form-label fw-semibold">Background Color</label>
                      <input 
                        type="color" 
                        className="form-control form-control-color w-100" 
                        value={bgColor} 
                        onChange={(e) => setBgColor(e.target.value)} 
                      />
                    </div>
                    <div className="col-6 mb-3">
                      <label className="form-label fw-semibold">Button Color</label>
                      <input 
                        type="color" 
                        className="form-control form-control-color w-100" 
                        value={buttonColor} 
                        onChange={(e) => setButtonColor(e.target.value)} 
                      />
                    </div>
                  </div>
                </div>
                <div className="modal-footer bg-light">
                  <button type="button" className="btn btn-secondary" onClick={() => setShowSettingsModal(false)}>Cancel</button>
                  <button type="submit" className="btn btn-primary">Save Changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}

      {/* ── DYNAMIC EDITING DIALOG FOR `২ কলাম ফিচার (৬×৬)` (TASK 1) ── */}
      {editingBlock && editingBlock.type === 'two_column_features' && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)', overflowY: 'auto' }}>
          <div className="modal-dialog modal-lg">
            <div className="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
              <div className="modal-header text-white" style={{ background: '#1e3a8a' }}>
                <h5 className="modal-title fw-bold d-flex align-items-center gap-2">
                  <List size={20} /> Add Features 2col
                </h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setEditingBlock(null)}></button>
              </div>

              {/* TABS CONTAINER */}
              <div className="bg-white px-3 pt-2 border-bottom">
                <ul className="nav nav-tabs border-0">
                  <li className="nav-item">
                    <button className="nav-link active fw-bold text-primary border-0 border-bottom border-primary border-3" type="button">Content</button>
                  </li>
                  <li className="nav-item">
                    <button className="nav-link text-secondary border-0" type="button" onClick={() => toast('Style & Animation tab settings loaded.')}>Style & Animation</button>
                  </li>
                </ul>
              </div>

              <div className="modal-body p-4 bg-light" style={{ maxHeight: '70vh', overflowY: 'auto' }}>
                {/* 1. Main Title */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">সেকশন মেইন টাইটেল</label>
                  <input 
                    type="text" 
                    className="form-control" 
                    value={editingBlock.data?.main_title || ''} 
                    onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.main_title = e.target.value;
                      setSections(updated);
                    }} 
                    placeholder="কেন ভালো মানের সাপ্লিমেন্ট বেছে নিবেন?"
                  />
                </div>

                {/* 2. Side by side Columns */}
                <div className="row g-4 mb-4">
                  {/* Left Column (Red Cross Problems) */}
                  <div className="col-md-6">
                    <div className="bg-white p-3 rounded border border-danger shadow-sm h-100">
                      <h6 className="fw-bold text-danger mb-3 d-flex align-items-center gap-1">
                        <AlertCircle size={18} /> বাম পাশ (অপকারিতা/ক্ষতি/সমস্যা)
                      </h6>
                      
                      <div className="mb-3">
                        <label className="form-label small fw-semibold text-secondary">বাম পাশের কলাম টাইটেল</label>
                        <input 
                          type="text" 
                          className="form-control" 
                          value={editingBlock.data?.left_title || ''} 
                          onChange={(e) => {
                            const updated = [...sections];
                            updated[editingBlock.index].data.left_title = e.target.value;
                            setSections(updated);
                          }} 
                        />
                      </div>

                      <div className="mb-3">
                        <label className="form-label small fw-semibold text-secondary">বাম পাশের আইকন টাইপ</label>
                        <select 
                          className="form-select" 
                          value={editingBlock.data?.left_icon || 'cross_red'}
                          onChange={(e) => {
                            const updated = [...sections];
                            updated[editingBlock.index].data.left_icon = e.target.value;
                            setSections(updated);
                          }}
                        >
                          <option value="cross_red">❌ ক্রস মার্ক (লাল)</option>
                          <option value="alert_orange">⚠️ ওয়ার্নিং মার্ক (হলুদ)</option>
                        </select>
                      </div>

                      <div className="mb-3">
                        <label className="form-label small fw-semibold text-secondary">বাম পাশের আইটেমগুলো</label>
                        <div className="d-flex flex-column gap-2">
                          {(editingBlock.data?.left_items || []).map((item, idx) => (
                            <div key={idx} className="d-flex gap-2">
                              <span className="text-danger align-self-center">❌</span>
                              <input 
                                type="text" 
                                className="form-control form-control-sm" 
                                value={item} 
                                onChange={(e) => {
                                  const updated = [...sections];
                                  updated[editingBlock.index].data.left_items[idx] = e.target.value;
                                  setSections(updated);
                                }} 
                              />
                              <button 
                                type="button" 
                                className="btn btn-sm btn-outline-danger" 
                                onClick={() => {
                                  const updated = [...sections];
                                  updated[editingBlock.index].data.left_items.splice(idx, 1);
                                  setSections(updated);
                                }}
                              >
                                ✕
                              </button>
                            </div>
                          ))}
                          <button 
                            type="button" 
                            className="btn btn-sm btn-outline-danger align-self-start mt-2 fw-semibold"
                            onClick={() => {
                              const updated = [...sections];
                              if (!updated[editingBlock.index].data.left_items) updated[editingBlock.index].data.left_items = [];
                              updated[editingBlock.index].data.left_items.push('');
                              setSections(updated);
                            }}
                          >
                            + আরেকটি যোগ করুন
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  {/* Right Column (Green Check Advantages) */}
                  <div className="col-md-6">
                    <div className="bg-white p-3 rounded border border-success shadow-sm h-100">
                      <h6 className="fw-bold text-success mb-3 d-flex align-items-center gap-1">
                        <CheckCircle2 size={18} /> ডান পাশ (উপকারিতা/লাভ/সমাধান)
                      </h6>
                      
                      <div className="mb-3">
                        <label className="form-label small fw-semibold text-secondary">ডান পাশের কলাম টাইটেল</label>
                        <input 
                          type="text" 
                          className="form-control" 
                          value={editingBlock.data?.right_title || ''} 
                          onChange={(e) => {
                            const updated = [...sections];
                            updated[editingBlock.index].data.right_title = e.target.value;
                            setSections(updated);
                          }} 
                        />
                      </div>

                      <div className="mb-3">
                        <label className="form-label small fw-semibold text-secondary">ডান পাশের আইকন টাইপ</label>
                        <select 
                          className="form-select" 
                          value={editingBlock.data?.right_icon || 'check_green'}
                          onChange={(e) => {
                            const updated = [...sections];
                            updated[editingBlock.index].data.right_icon = e.target.value;
                            setSections(updated);
                          }}
                        >
                          <option value="check_green">✔ চেক মার্ক (সবুজ)</option>
                          <option value="star_gold">⭐ স্টার মার্ক (সোনালী)</option>
                        </select>
                      </div>

                      <div className="mb-3">
                        <label className="form-label small fw-semibold text-secondary">ডান পাশের আইটেমগুলো</label>
                        <div className="d-flex flex-column gap-2">
                          {(editingBlock.data?.right_items || []).map((item, idx) => (
                            <div key={idx} className="d-flex gap-2">
                              <span className="text-success align-self-center">✔</span>
                              <input 
                                type="text" 
                                className="form-control form-control-sm" 
                                value={item} 
                                onChange={(e) => {
                                  const updated = [...sections];
                                  updated[editingBlock.index].data.right_items[idx] = e.target.value;
                                  setSections(updated);
                                }} 
                              />
                              <button 
                                type="button" 
                                className="btn btn-sm btn-outline-danger" 
                                onClick={() => {
                                  const updated = [...sections];
                                  updated[editingBlock.index].data.right_items.splice(idx, 1);
                                  setSections(updated);
                                }}
                              >
                                ✕
                              </button>
                            </div>
                          ))}
                          <button 
                            type="button" 
                            className="btn btn-sm btn-outline-success align-self-start mt-2 fw-semibold"
                            onClick={() => {
                              const updated = [...sections];
                              if (!updated[editingBlock.index].data.right_items) updated[editingBlock.index].data.right_items = [];
                              updated[editingBlock.index].data.right_items.push('');
                              setSections(updated);
                            }}
                          >
                            + আরেকটি যোগ করুন
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {/* 3. Bottom Section (Image + Bullet Highlights) */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <h6 className="fw-bold text-dark border-bottom pb-2 mb-3">✨ নিচের সেকশন (ইমেজ + বুলেট হাইলাইট - ঐচ্ছিক)</h6>
                  
                  <div className="row g-3">
                    <div className="col-md-6">
                      <label className="form-label small fw-semibold text-secondary">ইমেজ পজিশন (Layout Position)</label>
                      <select 
                        className="form-select" 
                        value={editingBlock.data?.bottom_layout || 'image_left'}
                        onChange={(e) => {
                          const updated = [...sections];
                          updated[editingBlock.index].data.bottom_layout = e.target.value;
                          setSections(updated);
                        }}
                      >
                        <option value="image_left">ইমেজ বামে, টেক্সট ডানে</option>
                        <option value="image_right">ইমেজ ডানে, টেক্সট বামে</option>
                      </select>
                    </div>

                    <div className="col-md-6">
                      <label className="form-label small fw-semibold text-secondary">আপলোড ইমেজ</label>
                      <input 
                        type="file" 
                        accept="image/*" 
                        className="form-control"
                        onChange={(e) => handleImageUpload(e, (uploadedPath) => {
                          const updated = [...sections];
                          updated[editingBlock.index].data.bottom_image = uploadedPath;
                          setSections(updated);
                        })} 
                      />
                      {editingBlock.data?.bottom_image && (
                        <div className="mt-2 border rounded p-1" style={{ maxWidth: '100px' }}>
                          <img src={editingBlock.data.bottom_image.startsWith('http') ? editingBlock.data.bottom_image : '/' + editingBlock.data.bottom_image} alt="uploaded" className="img-fluid rounded" />
                        </div>
                      )}
                    </div>
                  </div>

                  <div className="mt-3">
                    <label className="form-label small fw-semibold text-secondary">হাইলাইট টাইটেল</label>
                    <input 
                      type="text" 
                      className="form-control" 
                      value={editingBlock.data?.bottom_title || ''} 
                      onChange={(e) => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.bottom_title = e.target.value;
                        setSections(updated);
                      }} 
                    />
                  </div>

                  <div className="mt-3">
                    <label className="form-label small fw-semibold text-secondary">হাইলাইট বুলেট পয়েন্টগুলো</label>
                    <div className="d-flex flex-column gap-2 mt-1">
                      {(editingBlock.data?.bottom_bullets || []).map((bullet, idx) => (
                        <div key={idx} className="d-flex gap-2">
                          <span className="text-success align-self-center">✔</span>
                          <input 
                            type="text" 
                            className="form-control form-control-sm" 
                            value={bullet} 
                            onChange={(e) => {
                              const updated = [...sections];
                              updated[editingBlock.index].data.bottom_bullets[idx] = e.target.value;
                              setSections(updated);
                            }} 
                          />
                          <button 
                            type="button" 
                            className="btn btn-sm btn-outline-danger" 
                            onClick={() => {
                              const updated = [...sections];
                              updated[editingBlock.index].data.bottom_bullets.splice(idx, 1);
                              setSections(updated);
                            }}
                          >
                            ✕
                          </button>
                        </div>
                      ))}
                      <button 
                        type="button" 
                        className="btn btn-sm btn-outline-primary align-self-start mt-2 fw-semibold"
                        onClick={() => {
                          const updated = [...sections];
                          if (!updated[editingBlock.index].data.bottom_bullets) updated[editingBlock.index].data.bottom_bullets = [];
                          updated[editingBlock.index].data.bottom_bullets.push('');
                          setSections(updated);
                        }}
                      >
                        + আরেকটি বুলেট যোগ করুন
                      </button>
                    </div>
                  </div>
                </div>

                {/* 4. Additional consumption section */}
                <div className="bg-white p-3 rounded border shadow-sm">
                  <h6 className="fw-bold text-dark border-bottom pb-2 mb-3">ℹ অতিরিক্ত সেকশন / খাবার নিয়ম (ঐচ্ছিক)</h6>
                  
                  <div className="mb-3">
                    <label className="form-label small fw-semibold text-secondary">খাবার নিয়ম / অতিরিক্ত টাইটেল</label>
                    <input 
                      type="text" 
                      className="form-control" 
                      value={editingBlock.data?.extra_title || ''} 
                      onChange={(e) => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.extra_title = e.target.value;
                        setSections(updated);
                      }} 
                    />
                  </div>

                  <div>
                    <label className="form-label small fw-semibold text-secondary">খাবার নিয়ম / অতিরিক্ত বিবরণ (বিবরণীর প্রতি লাইনকে নতুন লাইনে লিখুন)</label>
                    <textarea 
                      className="form-control" 
                      rows="4" 
                      value={editingBlock.data?.extra_desc || ''} 
                      onChange={(e) => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.extra_desc = e.target.value;
                        setSections(updated);
                      }}
                    ></textarea>
                  </div>
                </div>
              </div>

              <div className="modal-footer bg-white border-top">
                <button type="button" className="btn btn-secondary fw-semibold px-4" onClick={() => setEditingBlock(null)}>Close</button>
                <button 
                  type="button" 
                  className="btn btn-primary fw-bold px-5" 
                  onClick={() => {
                    saveSectionsToDb(sections);
                    setEditingBlock(null);
                  }}
                >
                  Save Changes
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ── DYNAMIC EDITING DIALOG FOR `Video Section` ── */}
      {editingBlock && editingBlock.type === 'video_section' && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)', overflowY: 'auto' }}>
          <div className="modal-dialog modal-md modal-dialog-centered">
            <div className="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
              <div className="modal-header text-white" style={{ background: '#1e3a8a' }}>
                <h5 className="modal-title fw-bold d-flex align-items-center gap-2">
                  <Video size={20} /> Add Video
                </h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setEditingBlock(null)}></button>
              </div>

              {/* TABS CONTAINER */}
              <div className="bg-white px-3 pt-2 border-bottom">
                <ul className="nav nav-tabs border-0">
                  <li className="nav-item">
                    <button className="nav-link active fw-bold text-primary border-0 border-bottom border-primary border-3" type="button">Content</button>
                  </li>
                  <li className="nav-item">
                    <button className="nav-link text-secondary border-0" type="button" onClick={() => toast('Style & Animation tab settings loaded.')}>Style & Animation</button>
                  </li>
                </ul>
              </div>

              <div className="modal-body p-4 bg-light">
                {/* 1. Title */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">Title</label>
                  <input 
                    type="text" 
                    className="form-control" 
                    value={editingBlock.data?.title || ''} 
                    onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.title = e.target.value;
                      setSections(updated);
                    }} 
                    placeholder="Enter video title"
                  />
                </div>

                {/* 2. YouTube Video URL */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">YouTube Video URL</label>
                  <input 
                    type="text" 
                    className="form-control" 
                    value={editingBlock.data?.video_url || ''} 
                    onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.video_url = e.target.value;
                      setSections(updated);
                    }} 
                    placeholder="e.g. https://www.youtube.com/watch?v=..."
                  />
                </div>
              </div>

              <div className="modal-footer bg-white border-top">
                <button type="button" className="btn btn-secondary fw-semibold px-4" onClick={() => setEditingBlock(null)}>Close</button>
                <button 
                  type="button" 
                  className="btn btn-primary fw-bold px-5" 
                  onClick={() => {
                    saveSectionsToDb(sections);
                    setEditingBlock(null);
                  }}
                >
                  Save Changes
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ── DYNAMIC EDITING DIALOG FOR `Image Gallery` ── */}
      {editingBlock && editingBlock.type === 'image_gallery' && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)', overflowY: 'auto' }}>
          <div className="modal-dialog modal-lg modal-dialog-centered">
            <div className="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
              <div className="modal-header text-white" style={{ background: '#1e3a8a' }}>
                <h5 className="modal-title fw-bold d-flex align-items-center gap-2">
                  <Grid size={20} /> Add Gallery
                </h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setEditingBlock(null)}></button>
              </div>

              {/* TABS CONTAINER */}
              <div className="bg-white px-3 pt-2 border-bottom">
                <ul className="nav nav-tabs border-0">
                  <li className="nav-item">
                    <button className="nav-link active fw-bold text-primary border-0 border-bottom border-primary border-3" type="button">Content</button>
                  </li>
                  <li className="nav-item">
                    <button className="nav-link text-secondary border-0" type="button" onClick={() => toast('Style & Animation tab settings loaded.')}>Style & Animation</button>
                  </li>
                </ul>
              </div>

              <div className="modal-body p-4 bg-light" style={{ maxHeight: '70vh', overflowY: 'auto' }}>
                {/* 1. Section Title */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">Section Title</label>
                  <input 
                    type="text" 
                    className="form-control" 
                    value={editingBlock.data?.title || ''} 
                    onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.title = e.target.value;
                      setSections(updated);
                    }} 
                    placeholder="Our Gallery"
                  />
                </div>

                {/* 2. Upload Images (Multiple) */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">Upload Images (Multiple)</label>
                  <input 
                    type="file" 
                    multiple 
                    accept="image/*" 
                    className="form-control"
                    onChange={(e) => handleMultipleImagesUpload(e, editingBlock.index, editingBlock.data?.gallery || [])}
                  />
                  
                  {/* Gallery Previews with Deletions */}
                  {editingBlock.data?.gallery && editingBlock.data.gallery.length > 0 && (
                    <div className="mt-4 border-top pt-3">
                      <label className="form-label fw-bold text-secondary mb-3">Gallery Previews ({editingBlock.data.gallery.length} images)</label>
                      <div className="row g-2">
                        {editingBlock.data.gallery.map((img, idx) => (
                          <div key={idx} className="col-4 col-md-3 position-relative group">
                            <div className="border rounded overflow-hidden shadow-sm ratio ratio-1x1" style={{ backgroundColor: '#fff' }}>
                              <img 
                                src={img.startsWith('http') ? img : '/' + img} 
                                alt={`gallery-${idx}`} 
                                className="img-fluid object-cover w-100 h-100" 
                              />
                            </div>
                            <button
                              type="button"
                              onClick={() => {
                                const updated = [...sections];
                                updated[editingBlock.index].data.gallery.splice(idx, 1);
                                setSections(updated);
                                toast.success('Image removed!');
                              }}
                              className="btn btn-danger btn-sm rounded-circle shadow position-absolute"
                              style={{ top: '-5px', right: '-5px', zIndex: 10, width: '24px', height: '24px', padding: 0, display: 'flex', alignItems: 'center', justifyContent: 'center' }}
                            >
                              ✕
                            </button>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}
                </div>
              </div>

              <div className="modal-footer bg-white border-top">
                <button type="button" className="btn btn-secondary fw-semibold px-4" onClick={() => setEditingBlock(null)}>Close</button>
                <button 
                  type="button" 
                  className="btn btn-primary fw-bold px-5" 
                  onClick={() => {
                    saveSectionsToDb(sections);
                    setEditingBlock(null);
                  }}
                >
                  Save Changes
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default AdminLandingPageBuilder;
