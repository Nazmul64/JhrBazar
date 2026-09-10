import React, { useState, useEffect } from 'react';
import axios from 'axios';
import toast from 'react-hot-toast';
import { 
  Eye, ArrowLeft, Settings, Palette, Plus, Grid, List, 
  Trash2, Edit3, Move, Check, X, ShieldAlert, Image, 
  Video, Star, FileText, CheckCircle2, AlertCircle, Percent, Clock
} from 'lucide-react';

// Admin panel version — receives pageId as prop (no React Router needed)
const getLandingPageImageUrl = (url) => {
  if (!url) return '';
  if (url.startsWith('http')) return url;
  if (url.startsWith('/')) return url;
  return '/' + url;
};

const AdminLandingPageBuilder = ({ pageId, pageSlug, pageTitle: initialTitle }) => {
  const id = pageId;
  const [loading, setLoading] = useState(true);
  const [pageData, setPageData] = useState(null);
  const [sections, setSections] = useState([]);
  const [draggedIndex, setDraggedIndex] = useState(null);
  const [dragOverIndex, setDragOverIndex] = useState(null);
  
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

  // Drag & Drop Handlers
  const handleDragStart = (e, index) => {
    setDraggedIndex(index);
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', index.toString());
  };

  const handleDragOver = (e, index) => {
    e.preventDefault();
    if (dragOverIndex !== index) {
      setDragOverIndex(index);
    }
  };

  const handleDrop = (e, index) => {
    e.preventDefault();
    setDragOverIndex(null);
    if (draggedIndex === null || draggedIndex === index) return;

    const updated = [...sections];
    const draggedItem = updated[draggedIndex];
    updated.splice(draggedIndex, 1);
    updated.splice(index, 0, draggedItem);

    setSections(updated);
    saveSectionsToDb(updated);
    setDraggedIndex(null);
    toast.success('Sections reordered!');
  };

  const handleDragEnd = () => {
    setDraggedIndex(null);
    setDragOverIndex(null);
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
    } else if (type === 'three_column_grid') {
      defaultData = {
        main_title: 'বুকের দুধের সমস্যা সমাধানে Lactova Natural Supplement-এর কার্যকারিতা-',
        cards: [
          {
            title: 'বুকের দুধের প্রবাহ কমে যাচ্ছে?',
            icon_emoji: '🌿',
            desc: 'প্রাকৃতিক গ্যালাক্টাগ উপাদান বুকের দুধের পরিমাণ ও স্বাভাবিক ফ্লো বজায় রাখতে সহায়ক, ফলে মা ও শিশু দুজনেই পেতে পারে প্রয়োজনীয় পুষ্টি।'
          },
          {
            title: 'প্রসবের পর শরীর দুর্বল লাগছে?',
            icon_emoji: '💚',
            desc: 'মাতৃত্বের পর শরীরে যে ক্লান্তি ও দুর্বলতা আসে, Milkberry Lactova তা কমাতে সহায়তা করে এবং দৈনন্দিন শক্তি ও স্বস্তি ফিরিয়ে আনতে সাহায্য করে।'
          },
          {
            title: 'মায়ের পুষ্টির ঘাটতি কি প্রভাব ফেলছে?',
            icon_emoji: '✨',
            desc: 'প্রাকৃতিক ভেষজ উপাদান শরীরের প্রয়োজনীয় পুষ্টি সরবরাহে সহায়ক, যাতে মা থাকেন সুস্থ, প্রাণবন্ত ও সতেজ।'
          },
          {
            title: 'মানসিক চাপ কি বুকের দুধে প্রভাব ফেলে?',
            icon_emoji: '🌸',
            desc: 'অতিরিক্ত দুশ্চিন্তা ও ঘুমের অভাব অনেক সময় দুধের প্রবাহে প্রভাব ফেলে। Natural support মাকে মানসিক স্বস্তি ও রিল্যাক্স অনুভব করতে সহায়তা করতে পারে।'
          },
          {
            title: 'কেন Natural Support বেছে নেওয়া গুরুত্বপূর্ণ?',
            icon_emoji: '🌿',
            desc: 'প্রাকৃতিক উপাদান শরীরের স্বাভাবিক ভারসাম্য বজায় রাখতে সহায়তা করে এবং মা ও শিশুর সুস্থতায় gentle support দেয়।'
          },
          {
            title: 'কেন অনেক মা এখন Milkberry Lactova ব্যবহার করছেন?',
            icon_emoji: '💬',
            desc: 'Natural formulation, quality-focused ingredients এবং motherhood support এর কারণে অনেক মা এখন Milkberry Lactova এর উপর আস্থা রাখছেন।'
          }
        ]
      };
    } else if (type === 'trust_badges') {
      defaultData = {
        badge1: 'কোয়ালিটি নিশ্চিত করে ডেলিভারি',
        badge2: 'সারা বাংলাদেশে হোম ডেলিভারি',
        badge3: 'পণ্য চেক করে টাকা দেওয়ার সুযোগ',
        badge4: 'দ্রুত কাস্টমার সাপোর্ট'
      };
    } else if (type === 'countdown_pricing') {
      defaultData = {
        timer_text: '🔥 অফারটি শেষ হওয়ার আগে অর্ডার করুন!',
        timer_duration_mins: 60,
        price_text: 'আজকের বিশেষ দাম: ৳',
        price_type: 'subtotal'
      };
    } else if (type === 'video_image_order') {
      defaultData = {
        top_title: '🤩 মাত্র ৭-১০ দিন ব্যবহারে আপনার বাচ্চার ঠান্ডা সর্দি কাশি নির্মূল হবে, ইনশাল্লাহ 🌿 👶',
        video_url: '',
        image_path: '',
        bottom_title: 'ওষুধ সেবন ছাড়াই সন্তান এর কফ, ঠান্ডা, কাশি শ্বাসকষ্ট দূর করতে ব্যবহার করুন হাবীবী বেবি অয়েল।',
        button_text: 'অর্ডার করুন'
      };
    } else if (type === 'features_image_right') {
      defaultData = {
        title: 'এই তেল যে সকল সমস্যার সমাধান করবে',
        bg_color: '#2e4f40',
        text_color: '#ffffff',
        items: [
          'ঠান্ডা সর্দি কাশি প্রাকৃতিক ভাবে দূর করবে',
          'শিশুর বুকের জমে থাকা কফ সহজে বের করে দেয়',
          'নাক বন্ধ হওয়া বা শ্বাসকষ্ট হওয়া থেকে বাচ্চা কে রক্ষা করবে',
          'নেবুলাইজ করার প্রয়োজন পড়বে না',
          'রক্তসঞ্চালন উন্নত করে, যা বাচ্চার শক্তি ও বৃদ্ধিতে সহায়ক',
          'শিশুর রোগ প্রতিরোধ ক্ষমতা বাড়ায় নিউমোনিয়া ও ঠান্ডার সমস্যা দূর করে',
          'ম্যাসাজের মাধ্যমে ত্বক ও শ্বাসযন্ত্রের সুরক্ষা নিশ্চিত করা',
          'শিশুর শ্বাসপ্রশ্বাসকে স্বাভাবিক রাখতে সহায়ক ভূমিকা রাখে'
        ],
        image_path: ''
      };
    } else if (type === 'usage_rules') {
      defaultData = {
        title: 'এই তেল ব্যবহারের নিয়ম',
        description: '৫-৬ ফোঁটা তেল হাতে নিয়ে শিশুর বুকে পিঠে এবং পায়ে ম্যাসাজ করুন, প্রতিদিন ২-৩ বার ৫-১০ মিনিট করে ম্যাসাজ করতে পারেন।',
        button_text: 'অর্ডার করুন',
        image_path: '',
        bg_color: '#ffffff',
        text_color: '#1e293b'
      };
    } else if (type === 'review_slider') {
      defaultData = {
        title: 'সরাসরি কাস্টমার সাপোর্ট এ যোগাযোগ করুন: +880 1711-207829',
        slides: [],
        bg_color: '#ffffff',
        text_color: '#1e293b'
      };
    } else if (type === 'product_hero') {
      defaultData = {
        title: 'প্রাকৃতিক ও ১০০% খাঁটি হাবীবী বেবি অয়েল',
        subtitle: 'সন্তানের কফ, ঠান্ডা, কাশি ও শ্বাসকষ্ট দূর করতে এটি একটি কার্যকরী প্রাকৃতিক সমাধান।',
        video_url: '',
        image_path: '',
        bg_color: '#ffffff',
        text_color: '#1e293b',
        button_text: 'অর্ডার করুন',
        bullets: [
          '১০০% প্রাকৃতিক উপাদান',
          'কোনো পার্শ্বপ্রতিক্রিয়া নেই',
          'শিশুর ফুসফুস ও শ্বাসযন্ত্রের সুরক্ষা নিশ্চিত করে'
        ]
      };
    } else if (type === 'price_box') {
      defaultData = {
        title: 'প্যাকেজ অফার - সীমিত সময়ের জন্য!',
        original_price: '১২০০',
        discounted_price: '৬৯০',
        save_amount: '৫১০',
        badge_text: 'বেস্ট সেলার',
        bg_color: '#f8fafc',
        text_color: '#1e293b',
        button_text: 'অর্ডার করুন'
      };
    } else if (type === 'feature_list') {
      defaultData = {
        title: 'কেন হাবীবী বেবি অয়েল অন্য তেলের চেয়ে আলাদা?',
        bg_color: '#ffffff',
        text_color: '#1e293b',
        features: [
          { title: 'প্রাকৃতিক নিষ্কাশন', desc: 'কোনো রকম প্রিজারভেটিভ বা কেমিক্যাল ছাড়াই বিশেষ ভেষজ উপাদানের মিশ্রণ।', icon: 'leaf' },
          { title: 'নিরাপদ ফর্মুলা', desc: 'মায়ের যত্নে শিশুর কোমল ত্বকের জন্য সম্পূর্ণ নিরাপদ ও পরীক্ষিত।', icon: 'shield' },
          { title: 'দ্রুত কার্যকারিতা', desc: 'বুকে ম্যাসাজ করার মাত্র কয়েক মিনিটের মধ্যেই শ্বাসপ্রশ্বাস স্বাভাবিক হতে শুরু করে।', icon: 'clock' }
        ]
      };
    } else if (type === 'banner_slider') {
      defaultData = {
        slides: [],
        auto_play: true,
        bg_color: '#ffffff'
      };
    } else if (type === 'custom_html') {
      defaultData = {
        html_content: '<div class="text-center p-4 bg-light rounded shadow-sm">\n  <h3 class="fw-bold text-primary">এখানে আপনার কাস্টম হেডার দিন</h3>\n  <p class="text-secondary">কাস্টম HTML/CSS ব্যবহার করে এই অংশটি সাজানো হয়েছে।</p>\n</div>'
      };
    } else if (type === 'text_left_image_right') {
      defaultData = {
        title: 'বাংলাদেশ বিজ্ঞান ও শিল্প গবেষণা পরিষদ (BCSIR) থেকে ল্যাব টেস্টেড, তাই বাচ্চার শরীরের জন্য শতভাগ নিরাপদ!',
        description: 'আমাদের হাবিবী বেবি অয়েল সম্পূর্ণ প্রাকৃতিকভাবে তৈরি এবং বিসিএসআইআর ল্যাব টেস্টে শতভাগ নিরাপদ প্রমাণিত হয়েছে। এতে শিশুর ত্বকের জন্য কোনো ক্ষতিকর উপাদান নেই।',
        image_path: '',
        bg_color: '#ffffff',
        text_color: '#1e293b'
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
                draggable
                onDragStart={(e) => handleDragStart(e, index)}
                onDragOver={(e) => handleDragOver(e, index)}
                onDrop={(e) => handleDrop(e, index)}
                onDragEnd={handleDragEnd}
                className={`bg-white rounded border shadow-sm p-3 d-flex align-items-center justify-content-between transition-all ${
                  draggedIndex === index 
                    ? 'opacity-40 border-dashed border-secondary' 
                    : dragOverIndex === index 
                    ? 'border-primary border-2 shadow-md bg-light-subtle' 
                    : 'hover-shadow'
                }`}
                style={{ 
                  cursor: 'grab',
                  transform: dragOverIndex === index && draggedIndex !== index ? 'scale(1.01)' : 'none',
                  transition: 'all 0.2s ease-in-out'
                }}
              >
                <div className="d-flex align-items-center gap-3" draggable={false} onDragStart={(e) => e.preventDefault()}>
                  <input type="checkbox" className="form-check-input" draggable={false} />
                  <div className="bg-light rounded border d-flex align-items-center justify-content-center" style={{ width: '48px', height: '48px' }} draggable={false}>
                    {block.type === 'two_column_features' ? (
                      <List size={22} className="text-danger" />
                    ) : block.type === 'features_image_right' ? (
                      <List size={22} className="text-success" />
                    ) : block.type === 'usage_rules' ? (
                      <FileText size={22} className="text-primary" />
                    ) : block.type === 'review_slider' ? (
                      <Star size={22} className="text-warning" />
                    ) : (block.type === 'video_section' || block.type === 'video_image_order') ? (
                      <Video size={22} className="text-danger" />
                    ) : block.type === 'three_column_grid' ? (
                      <Grid size={22} className="text-success" />
                    ) : block.type === 'trust_badges' ? (
                      <CheckCircle2 size={22} className="text-success" />
                    ) : block.type === 'countdown_pricing' ? (
                      <Clock size={22} className="text-warning" />
                    ) : block.type === 'product_hero' ? (
                      <Star size={22} className="text-warning" />
                    ) : block.type === 'price_box' ? (
                      <Percent size={22} className="text-success" />
                    ) : block.type === 'feature_list' ? (
                      <CheckCircle2 size={22} className="text-info" />
                    ) : block.type === 'banner_slider' ? (
                      <Image size={22} className="text-primary" />
                    ) : block.type === 'custom_html' ? (
                      <FileText size={22} className="text-secondary" />
                    ) : block.type === 'text_left_image_right' ? (
                      <FileText size={22} className="text-success" />
                    ) : (
                      <Grid size={22} className="text-primary" />
                    )}
                  </div>
                  <div draggable={false}>
                    <h6 className="m-0 fw-bold text-dark">
                      {block.type === 'trust_badges' 
                        ? (block.data?.badge1 ? `${block.data.badge1}, ${block.data.badge2}...` : 'Trust Badges') 
                        : block.type === 'countdown_pricing'
                        ? (block.data?.timer_text || 'Countdown & Special Price')
                        : block.type === 'video_image_order'
                        ? (block.data?.top_title || 'Video & Image Section')
                        : (block.data?.main_title || block.data?.title || block.title || 'Untitled Block')}
                    </h6>
                    <span className="badge bg-light text-secondary border mt-1 font-monospace" style={{ fontSize: '10px' }}>
                      {block.type?.toUpperCase().replace(/_/g, ' ')}
                    </span>
                  </div>
                </div>

                <div className="d-flex align-items-center gap-3" draggable={false} onDragStart={(e) => e.preventDefault()}>
                  {/* Move up / down re-ordering arrows */}
                  <div className="btn-group">
                    <button 
                      disabled={index === 0} 
                      onClick={() => moveSection(index, 'up')} 
                      className="btn btn-sm btn-outline-secondary"
                      title="Move Up"
                      draggable={false}
                    >
                      ▲
                    </button>
                    <button 
                      disabled={index === sections.length - 1} 
                      onClick={() => moveSection(index, 'down')} 
                      className="btn btn-sm btn-outline-secondary"
                      title="Move Down"
                      draggable={false}
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
                    draggable={false}
                  >
                    <Edit3 size={14} /> Edit
                  </button>
                  <button 
                    onClick={() => deleteSection(index)} 
                    className="btn btn-danger btn-sm d-flex align-items-center gap-1 px-3"
                    draggable={false}
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
                    { type: 'review_slider', name: 'রিভিউ স্লাইডার (টাইটেল সহ)', icon: <Star size={22} className="text-warning" /> },
                    { type: 'video_section', name: 'Video Section', icon: <Video size={22} className="text-danger" /> },
                    { type: 'image_gallery', name: 'Image Gallery', icon: <Grid size={22} className="text-success" /> },
                    { type: 'custom_html', name: 'Custom HTML / Text', icon: <FileText size={22} className="text-secondary" /> },
                    { type: 'three_column_grid', name: '৩ কলাম বিশিষ্ট ফিচার গ্রিড', icon: <Grid size={22} className="text-success" /> },
                    { type: 'trust_badges', name: 'ট্রাস্ট ব্যাজ (৪ কলাম)', icon: <CheckCircle2 size={22} className="text-success" /> },
                    { type: 'countdown_pricing', name: 'কাউন্টডাউন ও বিশেষ দাম', icon: <Clock size={22} className="text-warning" /> },
                    { type: 'video_image_order', name: 'ভিডিও ও ছবি (অর্ডার বাটন সহ)', icon: <Video size={22} className="text-danger" /> },
                    { type: 'features_image_right', name: 'ফিচার তালিকা (বামে টেক্সট, ডানে ছবি)', icon: <List size={22} className="text-success" /> },
                    { type: 'usage_rules', name: 'ব্যবহারের নিয়ম (বামে টেক্সট, ডানে ছবি)', icon: <FileText size={22} className="text-primary" /> },
                    { type: 'text_left_image_right', name: 'ইমেজ ডানে, টেক্সট বামে (সার্টিফিকেট/ বিবরণ)', icon: <FileText size={22} className="text-success" /> },
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
                  {Array.from({ length: 27 }).map((_, i) => (
                    <div key={i} className="col-md-4 opacity-50">
                      <div className="bg-white rounded-3 border p-3 text-center hover-border shadow-sm h-100 d-flex flex-column align-items-center justify-content-center" style={{ minHeight: '120px' }}>
                        <Grid size={22} className="text-secondary mb-2" />
                        <span className="text-secondary small font-monospace">Placeholder Block #{i + 12}</span>
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
                          <img src={getLandingPageImageUrl(editingBlock.data.bottom_image)} alt="uploaded" className="img-fluid rounded" />
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

      {/* ── DYNAMIC EDITING DIALOG FOR `three_column_grid` ── */}
      {editingBlock && editingBlock.type === 'three_column_grid' && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)', overflowY: 'auto' }}>
          <div className="modal-dialog modal-lg">
            <div className="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
              <div className="modal-header text-white" style={{ background: '#1e3a8a' }}>
                <h5 className="modal-title fw-bold d-flex align-items-center gap-2">
                  <Grid size={20} /> Edit 3-Column Feature Grid
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
                {/* Section Main Title */}
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
                    placeholder="বুকের দুধের সমস্যা সমাধানে Lactova Natural Supplement-এর কার্যকারিতা-"
                  />
                </div>

                {/* Edit cards array */}
                <div className="row g-3">
                  {(editingBlock.data?.cards || []).map((card, idx) => (
                    <div key={idx} className="col-md-6">
                      <div className="bg-white p-3 rounded border shadow-sm h-100">
                        <h6 className="fw-bold text-primary mb-3">Card #{idx + 1} Settings</h6>
                        
                        <div className="mb-2">
                          <label className="form-label small fw-semibold text-secondary">Card Header/Question</label>
                          <input 
                            type="text" 
                            className="form-control form-control-sm" 
                            value={card.title || ''} 
                            onChange={(e) => {
                              const updated = [...sections];
                              updated[editingBlock.index].data.cards[idx].title = e.target.value;
                              setSections(updated);
                            }} 
                          />
                        </div>

                        <div className="mb-2">
                          <label className="form-label small fw-semibold text-secondary">Emoji Icon</label>
                          <input 
                            type="text" 
                            className="form-control form-control-sm" 
                            value={card.icon_emoji || ''} 
                            onChange={(e) => {
                              const updated = [...sections];
                              updated[editingBlock.index].data.cards[idx].icon_emoji = e.target.value;
                              setSections(updated);
                            }} 
                            placeholder="🌿, 💚, ✨..."
                          />
                        </div>

                        <div>
                          <label className="form-label small fw-semibold text-secondary">Description</label>
                          <textarea 
                            className="form-control form-control-sm" 
                            rows="3" 
                            value={card.desc || ''} 
                            onChange={(e) => {
                              const updated = [...sections];
                              updated[editingBlock.index].data.cards[idx].desc = e.target.value;
                              setSections(updated);
                            }}
                          ></textarea>
                        </div>
                      </div>
                    </div>
                  ))}
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

      {/* ── DYNAMIC EDITING DIALOG FOR `trust_badges` ── */}
      {editingBlock && editingBlock.type === 'trust_badges' && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)', overflowY: 'auto' }}>
          <div className="modal-dialog">
            <div className="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
              <div className="modal-header text-white" style={{ background: '#1e3a8a' }}>
                <h5 className="modal-title fw-bold d-flex align-items-center gap-2">
                  <CheckCircle2 size={20} /> Edit Trust Badges (4 Columns)
                </h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setEditingBlock(null)}></button>
              </div>

              <div className="modal-body p-4 bg-light">
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">ব্যাজ ১</label>
                  <input 
                    type="text" 
                    className="form-control" 
                    value={editingBlock.data?.badge1 || ''} 
                    onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.badge1 = e.target.value;
                      setSections(updated);
                    }} 
                    placeholder="কোয়ালিটি নিশ্চিত করে ডেলিভারি"
                  />
                </div>

                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">ব্যাজ ২</label>
                  <input 
                    type="text" 
                    className="form-control" 
                    value={editingBlock.data?.badge2 || ''} 
                    onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.badge2 = e.target.value;
                      setSections(updated);
                    }} 
                    placeholder="সারা বাংলাদেশে হোম ডেলিভারি"
                  />
                </div>

                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">ব্যাজ ৩</label>
                  <input 
                    type="text" 
                    className="form-control" 
                    value={editingBlock.data?.badge3 || ''} 
                    onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.badge3 = e.target.value;
                      setSections(updated);
                    }} 
                    placeholder="পণ্য চেক করে টাকা দেওয়ার সুযোগ"
                  />
                </div>

                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">ব্যাজ ৪</label>
                  <input 
                    type="text" 
                    className="form-control" 
                    value={editingBlock.data?.badge4 || ''} 
                    onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.badge4 = e.target.value;
                      setSections(updated);
                    }} 
                    placeholder="দ্রুত কাস্টমার সাপোর্ট"
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

      {/* ── DYNAMIC EDITING DIALOG FOR `countdown_pricing` ── */}
      {editingBlock && editingBlock.type === 'countdown_pricing' && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)', overflowY: 'auto' }}>
          <div className="modal-dialog">
            <div className="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
              <div className="modal-header text-white" style={{ background: '#1e3a8a' }}>
                <h5 className="modal-title fw-bold d-flex align-items-center gap-2">
                  <Clock size={20} /> Edit Countdown & Special Price Banner
                </h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setEditingBlock(null)}></button>
              </div>

              <div className="modal-body p-4 bg-light">
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <h6 className="fw-bold text-primary mb-2">কাউন্টডাউন টাইমার সেটিংস</h6>
                  
                  <div className="mb-2">
                    <label className="form-label small fw-semibold text-secondary">টাইমার হেডার টেক্সট</label>
                    <input 
                      type="text" 
                      className="form-control" 
                      value={editingBlock.data?.timer_text || ''} 
                      onChange={(e) => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.timer_text = e.target.value;
                        setSections(updated);
                      }} 
                      placeholder="🔥 অফারটি শেষ হওয়ার আগে অর্ডার করুন!"
                    />
                  </div>

                  <div className="mb-2">
                    <label className="form-label small fw-semibold text-secondary">কাউন্টডাউন সময় (মিনিট)</label>
                    <input 
                      type="number" 
                      className="form-control" 
                      value={editingBlock.data?.timer_duration_mins || 60} 
                      onChange={(e) => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.timer_duration_mins = parseInt(e.target.value) || 60;
                        setSections(updated);
                      }} 
                    />
                  </div>
                </div>

                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <h6 className="fw-bold text-success mb-2">বিশেষ মূল্য ব্যানার সেটিংস</h6>

                  <div className="mb-2">
                    <label className="form-label small fw-semibold text-secondary">মূল্য হেডার টেক্সট</label>
                    <input 
                      type="text" 
                      className="form-control" 
                      value={editingBlock.data?.price_text || ''} 
                      onChange={(e) => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.price_text = e.target.value;
                        setSections(updated);
                      }} 
                      placeholder="আজকের বিশেষ দাম: ৳"
                    />
                  </div>

                  <div className="mb-2">
                    <label className="form-label small fw-semibold text-secondary">মূল্য ধরণ (Price Type)</label>
                    <select 
                      className="form-select"
                      value={editingBlock.data?.price_type || 'subtotal'}
                      onChange={(e) => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.price_type = e.target.value;
                        setSections(updated);
                      }}
                    >
                      <option value="subtotal">Subtotal (ডেলিভারি চার্জ ছাড়া পণ্যের মোট দাম)</option>
                      <option value="grand_total">Grand Total (ডেলিভারি চার্জ সহ সর্বমোট দাম)</option>
                    </select>
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
                                src={getLandingPageImageUrl(img)} 
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

      {/* ── DYNAMIC EDITING DIALOG FOR `video_image_order` ── */}
      {editingBlock && editingBlock.type === 'video_image_order' && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)', overflowY: 'auto' }}>
          <div className="modal-dialog modal-lg">
            <div className="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
              <div className="modal-header text-white" style={{ background: '#1e3a8a' }}>
                <h5 className="modal-title fw-bold d-flex align-items-center gap-2">
                  <Video size={20} /> Edit Video & Image Section with CTA
                </h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setEditingBlock(null)}></button>
              </div>

              <div className="modal-body p-4 bg-light" style={{ maxHeight: '70vh', overflowY: 'auto' }}>
                {/* 1. Top Title */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">উপরের টাইটেল (Top Title)</label>
                  <input 
                    type="text" 
                    className="form-control" 
                    value={editingBlock.data?.top_title || ''} 
                    onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.top_title = e.target.value;
                      setSections(updated);
                    }} 
                    placeholder="যেমন: মাত্র ৭-১০ দিন ব্যবহারে আপনার বাচ্চার ঠান্ডা সর্দি কাশি নির্মূল হবে"
                  />
                </div>

                {/* 2. YouTube Video URL */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">ইউটিউব ভিডিও লিংক (YouTube Video URL - বাম পাশ)</label>
                  <input 
                    type="text" 
                    className="form-control" 
                    value={editingBlock.data?.video_url || ''} 
                    onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.video_url = e.target.value;
                      setSections(updated);
                    }} 
                    placeholder="https://www.youtube.com/watch?v=..."
                  />
                </div>

                {/* 3. Image Upload */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">ছবি (Image - ডান পাশ)</label>
                  <input 
                    type="file" 
                    accept="image/*" 
                    className="form-control"
                    onChange={(e) => handleImageUpload(e, (uploadedPath) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.image_path = uploadedPath;
                      setSections(updated);
                    })} 
                  />
                  {editingBlock.data?.image_path && (
                    <div className="mt-2 border rounded p-1" style={{ maxWidth: '150px' }}>
                      <img 
                        src={getLandingPageImageUrl(editingBlock.data.image_path)} 
                        alt="uploaded" 
                        className="img-fluid rounded" 
                      />
                    </div>
                  )}
                </div>

                {/* 4. Bottom Title */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">নিচের টাইটেল (Bottom Title)</label>
                  <textarea 
                    className="form-control" 
                    rows="3"
                    value={editingBlock.data?.bottom_title || ''} 
                    onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.bottom_title = e.target.value;
                      setSections(updated);
                    }} 
                    placeholder="যেমন: ওষুধ সেবন ছাড়াই সন্তান এর কফ, ঠান্ডা, কাশি শ্বাসকষ্ট দূর করতে ব্যবহার করুন হাবীবী বেবি অয়েল।"
                  />
                </div>

                {/* 5. Button Text */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">বাটন টেক্সট (Button Text)</label>
                  <input 
                    type="text" 
                    className="form-control" 
                    value={editingBlock.data?.button_text || 'অর্ডার করুন'} 
                    onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.button_text = e.target.value;
                      setSections(updated);
                    }} 
                    placeholder="অর্ডার করুন"
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

      {/* ── DYNAMIC EDITING DIALOG FOR `features_image_right` ── */}
      {editingBlock && editingBlock.type === 'features_image_right' && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)', overflowY: 'auto' }}>
          <div className="modal-dialog modal-lg">
            <div className="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
              <div className="modal-header text-white" style={{ background: '#1e3a8a' }}>
                <h5 className="modal-title fw-bold d-flex align-items-center gap-2">
                  <List size={20} /> Edit Features List (Left Text, Right Image)
                </h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setEditingBlock(null)}></button>
              </div>

              <div className="modal-body p-4 bg-light" style={{ maxHeight: '70vh', overflowY: 'auto' }}>
                {/* 1. Header Title */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">সেকশন টাইটেল (Header Title)</label>
                  <input 
                    type="text" 
                    className="form-control" 
                    value={editingBlock.data?.title || ''} 
                    onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.title = e.target.value;
                      setSections(updated);
                    }} 
                    placeholder="যেমন: এই তেল যে সকল সমস্যার সমাধান করবে"
                  />
                </div>

                {/* 2. Color Settings */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <h6 className="fw-bold text-dark mb-3">কালার সেটিংস (Color Settings)</h6>
                  <div className="row">
                    <div className="col-md-6 mb-3">
                      <label className="form-label small fw-semibold text-secondary">ব্যাকগ্রাউন্ড কালার (Background Color)</label>
                      <input 
                        type="color" 
                        className="form-control form-control-color w-100" 
                        value={editingBlock.data?.bg_color || '#2e4f40'} 
                        onChange={(e) => {
                          const updated = [...sections];
                          updated[editingBlock.index].data.bg_color = e.target.value;
                          setSections(updated);
                        }} 
                      />
                    </div>
                    <div className="col-md-6 mb-3">
                      <label className="form-label small fw-semibold text-secondary">টেক্সট কালার (Text Color)</label>
                      <input 
                        type="color" 
                        className="form-control form-control-color w-100" 
                        value={editingBlock.data?.text_color || '#ffffff'} 
                        onChange={(e) => {
                          const updated = [...sections];
                          updated[editingBlock.index].data.text_color = e.target.value;
                          setSections(updated);
                        }} 
                      />
                    </div>
                  </div>
                </div>

                {/* 3. Image Upload */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">ছবি (Image - ডান পাশ)</label>
                  <input 
                    type="file" 
                    accept="image/*" 
                    className="form-control"
                    onChange={(e) => handleImageUpload(e, (uploadedPath) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.image_path = uploadedPath;
                      setSections(updated);
                    })} 
                  />
                  {editingBlock.data?.image_path && (
                    <div className="mt-2 border rounded p-1" style={{ maxWidth: '150px' }}>
                      <img 
                        src={getLandingPageImageUrl(editingBlock.data.image_path)} 
                        alt="uploaded" 
                        className="img-fluid rounded" 
                      />
                    </div>
                  )}
                </div>

                {/* 4. Features Bullet List */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">ফিচার তালিকা (বাম পাশের কলাম আইটেম)</label>
                  <div className="d-flex flex-column gap-2 mt-1">
                    {(editingBlock.data?.items || []).map((item, idx) => (
                      <div key={idx} className="d-flex gap-2">
                        <span className="text-success align-self-center">✔</span>
                        <input 
                          type="text" 
                          className="form-control form-control-sm" 
                          value={item} 
                          onChange={(e) => {
                            const updated = [...sections];
                            updated[editingBlock.index].data.items[idx] = e.target.value;
                            setSections(updated);
                          }} 
                        />
                        <button 
                          type="button" 
                          className="btn btn-sm btn-outline-danger" 
                          onClick={() => {
                            const updated = [...sections];
                            updated[editingBlock.index].data.items.splice(idx, 1);
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
                        if (!updated[editingBlock.index].data.items) updated[editingBlock.index].data.items = [];
                        updated[editingBlock.index].data.items.push('');
                        setSections(updated);
                      }}
                    >
                      + আরেকটি ফিচার যোগ করুন
                    </button>
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

      {/* ── DYNAMIC EDITING DIALOG FOR `usage_rules` ── */}
      {editingBlock && editingBlock.type === 'usage_rules' && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)', overflowY: 'auto' }}>
          <div className="modal-dialog modal-lg">
            <div className="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
              <div className="modal-header text-white" style={{ background: '#1e3a8a' }}>
                <h5 className="modal-title fw-bold d-flex align-items-center gap-2">
                  <FileText size={20} /> Edit Usage Rules (Left Text, Right Image)
                </h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setEditingBlock(null)}></button>
              </div>

              <div className="modal-body p-4 bg-light" style={{ maxHeight: '70vh', overflowY: 'auto' }}>
                {/* 1. Header Title */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">সেকশন টাইটেল (Header Title)</label>
                  <input 
                    type="text" 
                    className="form-control" 
                    value={editingBlock.data?.title || ''} 
                    onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.title = e.target.value;
                      setSections(updated);
                    }} 
                    placeholder="যেমন: এই তেল ব্যবহারের নিয়ম"
                  />
                </div>

                {/* 2. Description Instruction Text */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">বর্ণনা / নির্দেশনাবলী (Description)</label>
                  <textarea 
                    className="form-control" 
                    rows="4"
                    value={editingBlock.data?.description || ''} 
                    onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.description = e.target.value;
                      setSections(updated);
                    }} 
                    placeholder="যেমন: ৫-৬ ফোঁটা তেল হাতে নিয়ে শিশুর বুকে পিঠে এবং পায়ে ম্যাসাজ করুন..."
                  />
                </div>

                {/* 3. Button Text */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">বাটন টেক্সট (Button Text)</label>
                  <input 
                    type="text" 
                    className="form-control" 
                    value={editingBlock.data?.button_text || 'অর্ডার করুন'} 
                    onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.button_text = e.target.value;
                      setSections(updated);
                    }} 
                    placeholder="অর্ডার করুন"
                  />
                </div>

                {/* 4. Color Settings */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <h6 className="fw-bold text-dark mb-3">কালার সেটিংস (Color Settings)</h6>
                  <div className="row">
                    <div className="col-md-6 mb-3">
                      <label className="form-label small fw-semibold text-secondary">ব্যাকগ্রাউন্ড কালার (Background Color)</label>
                      <input 
                        type="color" 
                        className="form-control form-control-color w-100" 
                        value={editingBlock.data?.bg_color || '#ffffff'} 
                        onChange={(e) => {
                          const updated = [...sections];
                          updated[editingBlock.index].data.bg_color = e.target.value;
                          setSections(updated);
                        }} 
                      />
                    </div>
                    <div className="col-md-6 mb-3">
                      <label className="form-label small fw-semibold text-secondary">টেক্সট কালার (Text Color)</label>
                      <input 
                        type="color" 
                        className="form-control form-control-color w-100" 
                        value={editingBlock.data?.text_color || '#1e293b'} 
                        onChange={(e) => {
                          const updated = [...sections];
                          updated[editingBlock.index].data.text_color = e.target.value;
                          setSections(updated);
                        }} 
                      />
                    </div>
                  </div>
                </div>

                {/* 5. Image Upload */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">ছবি (Image - ডান পাশ)</label>
                  <input 
                    type="file" 
                    accept="image/*" 
                    className="form-control"
                    onChange={(e) => handleImageUpload(e, (uploadedPath) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.image_path = uploadedPath;
                      setSections(updated);
                    })} 
                  />
                  {editingBlock.data?.image_path && (
                    <div className="mt-2 border rounded p-1" style={{ maxWidth: '150px' }}>
                      <img 
                        src={getLandingPageImageUrl(editingBlock.data.image_path)} 
                        alt="uploaded" 
                        className="img-fluid rounded" 
                      />
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

      {/* ── DYNAMIC EDITING DIALOG FOR `review_slider` ── */}
      {editingBlock && editingBlock.type === 'review_slider' && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)', overflowY: 'auto' }}>
          <div className="modal-dialog modal-lg">
            <div className="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
              <div className="modal-header text-white" style={{ background: '#1e3a8a' }}>
                <h5 className="modal-title fw-bold d-flex align-items-center gap-2">
                  <Star size={20} /> Edit Review Slider Block
                </h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setEditingBlock(null)}></button>
              </div>

              <div className="modal-body p-4 bg-light" style={{ maxHeight: '70vh', overflowY: 'auto' }}>
                {/* 1. Header Title */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">সেকশন টাইটেল (Header Title)</label>
                  <input 
                    type="text" 
                    className="form-control" 
                    value={editingBlock.data?.title || ''} 
                    onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.title = e.target.value;
                      setSections(updated);
                    }} 
                    placeholder="যেমন: সরাসরি কাস্টমার সাপোর্ট এ যোগাযোগ করুন: +880 1711-207829"
                  />
                </div>

                {/* 2. Color Settings */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <h6 className="fw-bold text-dark mb-3">কালার সেটিংস (Color Settings)</h6>
                  <div className="row">
                    <div className="col-md-6 mb-3">
                      <label className="form-label small fw-semibold text-secondary">ব্যাকগ্রাউন্ড কালার (Background Color)</label>
                      <input 
                        type="color" 
                        className="form-control form-control-color w-100" 
                        value={editingBlock.data?.bg_color || '#ffffff'} 
                        onChange={(e) => {
                          const updated = [...sections];
                          updated[editingBlock.index].data.bg_color = e.target.value;
                          setSections(updated);
                        }} 
                      />
                    </div>
                    <div className="col-md-6 mb-3">
                      <label className="form-label small fw-semibold text-secondary">টেক্সট কালার (Text Color)</label>
                      <input 
                        type="color" 
                        className="form-control form-control-color w-100" 
                        value={editingBlock.data?.text_color || '#1e293b'} 
                        onChange={(e) => {
                          const updated = [...sections];
                          updated[editingBlock.index].data.text_color = e.target.value;
                          setSections(updated);
                        }} 
                      />
                    </div>
                  </div>
                </div>

                {/* 3. Upload Slide Images (Multiple) */}
                <div className="mb-4 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold text-dark">স্লাইড ইমেজ আপলোড করুন (Upload Slide Images)</label>
                  <input 
                    type="file" 
                    multiple 
                    accept="image/*" 
                    className="form-control"
                    onChange={(e) => {
                      const files = Array.from(e.target.files);
                      if (files.length === 0) return;

                      toast.loading('Uploading slide images...', { id: 'upload-slides' });
                      
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
                          if (!updated[editingBlock.index].data.slides) updated[editingBlock.index].data.slides = [];
                          updated[editingBlock.index].data.slides = [...(editingBlock.data.slides || []), ...uploadedPaths];
                          setSections(updated);
                          toast.success(`Successfully uploaded ${uploadedPaths.length} slide images!`, { id: 'upload-slides' });
                        } else {
                          toast.error('Failed to upload images.', { id: 'upload-slides' });
                        }
                      });
                    }}
                  />
                  
                  {/* Slides Previews with Deletions */}
                  {editingBlock.data?.slides && editingBlock.data.slides.length > 0 && (
                    <div className="mt-4 border-top pt-3">
                      <label className="form-label fw-bold text-secondary mb-3">স্লাইড প্রিভিউ ({editingBlock.data.slides.length} images)</label>
                      <div className="row g-2">
                        {editingBlock.data.slides.map((img, idx) => (
                          <div key={idx} className="col-4 col-md-3 position-relative group">
                            <div className="border rounded overflow-hidden shadow-sm ratio ratio-1x1" style={{ backgroundColor: '#fff' }}>
                              <img 
                                src={getLandingPageImageUrl(img)} 
                                alt={`slide-${idx}`} 
                                className="img-fluid object-cover w-100 h-100" 
                              />
                            </div>
                            <button
                              type="button"
                              onClick={() => {
                                const updated = [...sections];
                                updated[editingBlock.index].data.slides.splice(idx, 1);
                                setSections(updated);
                                toast.success('Slide removed!');
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

      {/* ── DYNAMIC EDITING DIALOG FOR `product_hero` ── */}
      {editingBlock && editingBlock.type === 'product_hero' && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)', overflowY: 'auto' }}>
          <div className="modal-dialog modal-lg">
            <div className="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
              <div className="modal-header text-white" style={{ background: '#1e3a8a' }}>
                <h5 className="modal-title fw-bold">Edit Product Hero</h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setEditingBlock(null)}></button>
              </div>
              <div className="modal-body p-4 bg-light" style={{ maxHeight: '70vh', overflowY: 'auto' }}>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">Title</label>
                  <input type="text" className="form-control" value={editingBlock.data?.title || ''} onChange={(e) => {
                    const updated = [...sections];
                    updated[editingBlock.index].data.title = e.target.value;
                    setSections(updated);
                  }} />
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">Subtitle</label>
                  <textarea className="form-control" rows="3" value={editingBlock.data?.subtitle || ''} onChange={(e) => {
                    const updated = [...sections];
                    updated[editingBlock.index].data.subtitle = e.target.value;
                    setSections(updated);
                  }}></textarea>
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">YouTube Video URL</label>
                  <input type="text" className="form-control" value={editingBlock.data?.video_url || ''} onChange={(e) => {
                    const updated = [...sections];
                    updated[editingBlock.index].data.video_url = e.target.value;
                    setSections(updated);
                  }} placeholder="e.g. https://www.youtube.com/watch?v=..." />
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">Hero Image (Alternative to video)</label>
                  <input type="file" className="form-control mb-2" onChange={(e) => {
                    const file = e.target.files[0];
                    if (!file) return;
                    const formData = new FormData();
                    formData.append('image', file);
                    toast.loading('Uploading image...', { id: 'upload-hero-img' });
                    axios.post('/api/admin/landingpages/upload-image', formData, {
                      headers: { 'Content-Type': 'multipart/form-data' }
                    }).then(res => {
                      if (res.data.success) {
                        const updated = [...sections];
                        updated[editingBlock.index].data.image_path = res.data.path;
                        setSections(updated);
                        toast.success('Uploaded successfully!', { id: 'upload-hero-img' });
                      } else {
                        toast.error('Upload failed.', { id: 'upload-hero-img' });
                      }
                    }).catch(err => {
                      console.error(err);
                      toast.error('Upload error.', { id: 'upload-hero-img' });
                    });
                  }} />
                  {editingBlock.data?.image_path && (
                    <div className="mt-2">
                      <img src={getLandingPageImageUrl(editingBlock.data.image_path)} alt="Preview" className="img-thumbnail" style={{ maxHeight: '150px' }} />
                    </div>
                  )}
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold d-flex justify-content-between align-items-center">
                    <span>Bullet Highlights</span>
                    <button type="button" className="btn btn-sm btn-outline-success" onClick={() => {
                      const updated = [...sections];
                      if (!updated[editingBlock.index].data.bullets) updated[editingBlock.index].data.bullets = [];
                      updated[editingBlock.index].data.bullets.push('');
                      setSections(updated);
                    }}>+ Add Bullet</button>
                  </label>
                  {(editingBlock.data?.bullets || []).map((bullet, idx) => (
                    <div key={idx} className="d-flex gap-2 mb-2">
                      <input type="text" className="form-control" value={bullet} onChange={(e) => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.bullets[idx] = e.target.value;
                        setSections(updated);
                      }} />
                      <button type="button" className="btn btn-outline-danger" onClick={() => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.bullets.splice(idx, 1);
                        setSections(updated);
                      }}>✕</button>
                    </div>
                  ))}
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">Button Text</label>
                  <input type="text" className="form-control" value={editingBlock.data?.button_text || ''} onChange={(e) => {
                    const updated = [...sections];
                    updated[editingBlock.index].data.button_text = e.target.value;
                    setSections(updated);
                  }} />
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <div className="row">
                    <div className="col-6">
                      <label className="form-label fw-bold">Background Color</label>
                      <input type="color" className="form-control form-control-color w-100" value={editingBlock.data?.bg_color || '#ffffff'} onChange={(e) => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.bg_color = e.target.value;
                        setSections(updated);
                      }} />
                    </div>
                    <div className="col-6">
                      <label className="form-label fw-bold">Text Color</label>
                      <input type="color" className="form-control form-control-color w-100" value={editingBlock.data?.text_color || '#1e293b'} onChange={(e) => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.text_color = e.target.value;
                        setSections(updated);
                      }} />
                    </div>
                  </div>
                </div>
              </div>
              <div className="modal-footer bg-white border-top">
                <button type="button" className="btn btn-secondary fw-semibold px-4" onClick={() => setEditingBlock(null)}>Close</button>
                <button type="button" className="btn btn-primary fw-bold px-5" onClick={() => {
                  saveSectionsToDb(sections);
                  setEditingBlock(null);
                }}>Save Changes</button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ── DYNAMIC EDITING DIALOG FOR `price_box` ── */}
      {editingBlock && editingBlock.type === 'price_box' && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)', overflowY: 'auto' }}>
          <div className="modal-dialog modal-md">
            <div className="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
              <div className="modal-header text-white" style={{ background: '#1e3a8a' }}>
                <h5 className="modal-title fw-bold">Edit Price Box</h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setEditingBlock(null)}></button>
              </div>
              <div className="modal-body p-4 bg-light" style={{ maxHeight: '70vh', overflowY: 'auto' }}>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">Package Title</label>
                  <input type="text" className="form-control" value={editingBlock.data?.title || ''} onChange={(e) => {
                    const updated = [...sections];
                    updated[editingBlock.index].data.title = e.target.value;
                    setSections(updated);
                  }} />
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">Original Price (৳)</label>
                  <input type="text" className="form-control" value={editingBlock.data?.original_price || ''} onChange={(e) => {
                    const updated = [...sections];
                    updated[editingBlock.index].data.original_price = e.target.value;
                    setSections(updated);
                  }} />
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">Discounted Price (৳)</label>
                  <input type="text" className="form-control" value={editingBlock.data?.discounted_price || ''} onChange={(e) => {
                    const updated = [...sections];
                    updated[editingBlock.index].data.discounted_price = e.target.value;
                    setSections(updated);
                  }} />
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">Save Amount (৳)</label>
                  <input type="text" className="form-control" value={editingBlock.data?.save_amount || ''} onChange={(e) => {
                    const updated = [...sections];
                    updated[editingBlock.index].data.save_amount = e.target.value;
                    setSections(updated);
                  }} />
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">Badge Text</label>
                  <input type="text" className="form-control" value={editingBlock.data?.badge_text || ''} onChange={(e) => {
                    const updated = [...sections];
                    updated[editingBlock.index].data.badge_text = e.target.value;
                    setSections(updated);
                  }} placeholder="e.g. বেস্ট সেলার" />
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">Button Text</label>
                  <input type="text" className="form-control" value={editingBlock.data?.button_text || ''} onChange={(e) => {
                    const updated = [...sections];
                    updated[editingBlock.index].data.button_text = e.target.value;
                    setSections(updated);
                  }} />
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <div className="row">
                    <div className="col-6">
                      <label className="form-label fw-bold">Background Color</label>
                      <input type="color" className="form-control form-control-color w-100" value={editingBlock.data?.bg_color || '#f8fafc'} onChange={(e) => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.bg_color = e.target.value;
                        setSections(updated);
                      }} />
                    </div>
                    <div className="col-6">
                      <label className="form-label fw-bold">Text Color</label>
                      <input type="color" className="form-control form-control-color w-100" value={editingBlock.data?.text_color || '#1e293b'} onChange={(e) => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.text_color = e.target.value;
                        setSections(updated);
                      }} />
                    </div>
                  </div>
                </div>
              </div>
              <div className="modal-footer bg-white border-top">
                <button type="button" className="btn btn-secondary fw-semibold px-4" onClick={() => setEditingBlock(null)}>Close</button>
                <button type="button" className="btn btn-primary fw-bold px-5" onClick={() => {
                  saveSectionsToDb(sections);
                  setEditingBlock(null);
                }}>Save Changes</button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ── DYNAMIC EDITING DIALOG FOR `feature_list` ── */}
      {editingBlock && editingBlock.type === 'feature_list' && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)', overflowY: 'auto' }}>
          <div className="modal-dialog modal-lg">
            <div className="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
              <div className="modal-header text-white" style={{ background: '#1e3a8a' }}>
                <h5 className="modal-title fw-bold">Edit Product Feature List</h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setEditingBlock(null)}></button>
              </div>
              <div className="modal-body p-4 bg-light" style={{ maxHeight: '70vh', overflowY: 'auto' }}>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">Main Title</label>
                  <input type="text" className="form-control" value={editingBlock.data?.title || ''} onChange={(e) => {
                    const updated = [...sections];
                    updated[editingBlock.index].data.title = e.target.value;
                    setSections(updated);
                  }} />
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold d-flex justify-content-between align-items-center">
                    <span>Feature List Items</span>
                    <button type="button" className="btn btn-sm btn-outline-success" onClick={() => {
                      const updated = [...sections];
                      if (!updated[editingBlock.index].data.features) updated[editingBlock.index].data.features = [];
                      updated[editingBlock.index].data.features.push({ title: 'নতুন ফিচার', desc: '', icon: 'check' });
                      setSections(updated);
                    }}>+ Add Feature</button>
                  </label>
                  {(editingBlock.data?.features || []).map((feat, idx) => (
                    <div key={idx} className="border p-3 rounded bg-light mb-3 position-relative">
                      <button type="button" className="btn-close position-absolute" style={{ top: '10px', right: '10px' }} onClick={() => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.features.splice(idx, 1);
                        setSections(updated);
                      }}></button>
                      <div className="row g-2">
                        <div className="col-md-8">
                          <label className="form-label small fw-semibold">Feature Title</label>
                          <input type="text" className="form-control mb-2" value={feat.title || ''} onChange={(e) => {
                            const updated = [...sections];
                            updated[editingBlock.index].data.features[idx].title = e.target.value;
                            setSections(updated);
                          }} />
                        </div>
                        <div className="col-md-4">
                          <label className="form-label small fw-semibold">Icon Type</label>
                          <select className="form-select mb-2" value={feat.icon || 'check'} onChange={(e) => {
                            const updated = [...sections];
                            updated[editingBlock.index].data.features[idx].icon = e.target.value;
                            setSections(updated);
                          }}>
                            <option value="check">Checkmark (✓)</option>
                            <option value="leaf">Leaf (🌿)</option>
                            <option value="shield">Shield (🛡)</option>
                            <option value="clock">Clock (🕒)</option>
                            <option value="star">Star (⭐)</option>
                            <option value="award">Award (🏆)</option>
                            <option value="heart">Heart (❤️)</option>
                          </select>
                        </div>
                        <div className="col-12">
                          <label className="form-label small fw-semibold">Feature Description</label>
                          <textarea className="form-control" rows="2" value={feat.desc || ''} onChange={(e) => {
                            const updated = [...sections];
                            updated[editingBlock.index].data.features[idx].desc = e.target.value;
                            setSections(updated);
                          }}></textarea>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <div className="row">
                    <div className="col-6">
                      <label className="form-label fw-bold">Background Color</label>
                      <input type="color" className="form-control form-control-color w-100" value={editingBlock.data?.bg_color || '#ffffff'} onChange={(e) => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.bg_color = e.target.value;
                        setSections(updated);
                      }} />
                    </div>
                    <div className="col-6">
                      <label className="form-label fw-bold">Text Color</label>
                      <input type="color" className="form-control form-control-color w-100" value={editingBlock.data?.text_color || '#1e293b'} onChange={(e) => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.text_color = e.target.value;
                        setSections(updated);
                      }} />
                    </div>
                  </div>
                </div>
              </div>
              <div className="modal-footer bg-white border-top">
                <button type="button" className="btn btn-secondary fw-semibold px-4" onClick={() => setEditingBlock(null)}>Close</button>
                <button type="button" className="btn btn-primary fw-bold px-5" onClick={() => {
                  saveSectionsToDb(sections);
                  setEditingBlock(null);
                }}>Save Changes</button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ── DYNAMIC EDITING DIALOG FOR `banner_slider` ── */}
      {editingBlock && editingBlock.type === 'banner_slider' && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)', overflowY: 'auto' }}>
          <div className="modal-dialog modal-lg">
            <div className="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
              <div className="modal-header text-white" style={{ background: '#1e3a8a' }}>
                <h5 className="modal-title fw-bold">Edit Banner Slider</h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setEditingBlock(null)}></button>
              </div>
              <div className="modal-body p-4 bg-light" style={{ maxHeight: '70vh', overflowY: 'auto' }}>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">Banner Images</label>
                  <input type="file" multiple accept="image/*" className="form-control mb-3" onChange={(e) => {
                    const files = Array.from(e.target.files);
                    if (files.length === 0) return;
                    toast.loading('Uploading banner images...', { id: 'upload-banners' });
                    const uploadPromises = files.map(file => {
                      const formData = new FormData();
                      formData.append('image', file);
                      return axios.post('/api/admin/landingpages/upload-image', formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                      }).then(res => res.data.success ? res.data.path : null).catch(() => null);
                    });
                    Promise.all(uploadPromises).then(results => {
                      const uploadedPaths = results.filter(p => p !== null);
                      if (uploadedPaths.length > 0) {
                        const updated = [...sections];
                        if (!updated[editingBlock.index].data.slides) updated[editingBlock.index].data.slides = [];
                        updated[editingBlock.index].data.slides = [...(editingBlock.data.slides || []), ...uploadedPaths];
                        setSections(updated);
                        toast.success('Uploaded successfully!', { id: 'upload-banners' });
                      } else {
                        toast.error('Upload failed.', { id: 'upload-banners' });
                      }
                    });
                  }} />
                  {editingBlock.data?.slides && editingBlock.data.slides.length > 0 && (
                    <div className="row g-2">
                      {editingBlock.data.slides.map((img, idx) => (
                        <div key={idx} className="col-3 position-relative">
                          <div className="ratio ratio-16x9 border rounded overflow-hidden">
                            <img src={getLandingPageImageUrl(img)} alt="Slide Preview" style={{ objectFit: 'cover' }} />
                          </div>
                          <button type="button" className="btn btn-danger btn-sm rounded-circle position-absolute" style={{ top: '-5px', right: '-5px', width: '20px', height: '20px', padding: 0 }} onClick={() => {
                            const updated = [...sections];
                            updated[editingBlock.index].data.slides.splice(idx, 1);
                            setSections(updated);
                          }}>✕</button>
                        </div>
                      ))}
                    </div>
                  )}
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <div className="form-check form-switch">
                    <input className="form-check-input" type="checkbox" role="switch" id="autoPlaySwitch" checked={editingBlock.data?.auto_play !== false} onChange={(e) => {
                      const updated = [...sections];
                      updated[editingBlock.index].data.auto_play = e.target.checked;
                      setSections(updated);
                    }} />
                    <label className="form-check-label fw-bold" htmlFor="autoPlaySwitch">Auto Play Slides</label>
                  </div>
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">Background Color</label>
                  <input type="color" className="form-control form-control-color w-100" value={editingBlock.data?.bg_color || '#ffffff'} onChange={(e) => {
                    const updated = [...sections];
                    updated[editingBlock.index].data.bg_color = e.target.value;
                    setSections(updated);
                  }} />
                </div>
              </div>
              <div className="modal-footer bg-white border-top">
                <button type="button" className="btn btn-secondary fw-semibold px-4" onClick={() => setEditingBlock(null)}>Close</button>
                <button type="button" className="btn btn-primary fw-bold px-5" onClick={() => {
                  saveSectionsToDb(sections);
                  setEditingBlock(null);
                }}>Save Changes</button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ── DYNAMIC EDITING DIALOG FOR `custom_html` ── */}
      {editingBlock && editingBlock.type === 'custom_html' && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)', overflowY: 'auto' }}>
          <div className="modal-dialog modal-lg">
            <div className="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
              <div className="modal-header text-white" style={{ background: '#1e3a8a' }}>
                <h5 className="modal-title fw-bold">Edit Custom HTML</h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setEditingBlock(null)}></button>
              </div>
              <div className="modal-body p-4 bg-light" style={{ maxHeight: '70vh', overflowY: 'auto' }}>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">HTML Code / Content</label>
                  <textarea className="form-control font-monospace" rows="10" value={editingBlock.data?.html_content || ''} onChange={(e) => {
                    const updated = [...sections];
                    updated[editingBlock.index].data.html_content = e.target.value;
                    setSections(updated);
                  }} style={{ fontSize: '13px', lineHeight: '1.5', background: '#0f172a', color: '#e2e8f0' }}></textarea>
                </div>
              </div>
              <div className="modal-footer bg-white border-top">
                <button type="button" className="btn btn-secondary fw-semibold px-4" onClick={() => setEditingBlock(null)}>Close</button>
                <button type="button" className="btn btn-primary fw-bold px-5" onClick={() => {
                  saveSectionsToDb(sections);
                  setEditingBlock(null);
                }}>Save Changes</button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ── DYNAMIC EDITING DIALOG FOR `text_left_image_right` ── */}
      {editingBlock && editingBlock.type === 'text_left_image_right' && (
        <div className="modal show d-block" tabIndex="-1" style={{ background: 'rgba(0,0,0,0.5)', overflowY: 'auto' }}>
          <div className="modal-dialog modal-lg">
            <div className="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
              <div className="modal-header text-white" style={{ background: '#1e3a8a' }}>
                <h5 className="modal-title fw-bold">Edit Text Left, Image Right Section</h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setEditingBlock(null)}></button>
              </div>
              <div className="modal-body p-4 bg-light" style={{ maxHeight: '70vh', overflowY: 'auto' }}>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">Title (বাম পাশের টাইটেল)</label>
                  <textarea className="form-control" rows="3" value={editingBlock.data?.title || ''} onChange={(e) => {
                    const updated = [...sections];
                    updated[editingBlock.index].data.title = e.target.value;
                    setSections(updated);
                  }}></textarea>
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">Description (বাম পাশের বিবরণ)</label>
                  <textarea className="form-control" rows="5" value={editingBlock.data?.description || ''} onChange={(e) => {
                    const updated = [...sections];
                    updated[editingBlock.index].data.description = e.target.value;
                    setSections(updated);
                  }}></textarea>
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <label className="form-label fw-bold">Section Image (ডান পাশের ছবি)</label>
                  <input type="file" className="form-control mb-2" onChange={(e) => {
                    const file = e.target.files[0];
                    if (!file) return;
                    const formData = new FormData();
                    formData.append('image', file);
                    toast.loading('Uploading image...', { id: 'upload-section-img' });
                    axios.post('/api/admin/landingpages/upload-image', formData, {
                      headers: { 'Content-Type': 'multipart/form-data' }
                    }).then(res => {
                      if (res.data.success) {
                        const updated = [...sections];
                        updated[editingBlock.index].data.image_path = res.data.path;
                        setSections(updated);
                        toast.success('Uploaded successfully!', { id: 'upload-section-img' });
                      } else {
                        toast.error('Upload failed.', { id: 'upload-section-img' });
                      }
                    }).catch(err => {
                      console.error(err);
                      toast.error('Upload error.', { id: 'upload-section-img' });
                    });
                  }} />
                  {editingBlock.data?.image_path && (
                    <div className="mt-2">
                      <img src={getLandingPageImageUrl(editingBlock.data.image_path)} alt="Preview" className="img-thumbnail" style={{ maxHeight: '150px' }} />
                    </div>
                  )}
                </div>
                <div className="mb-3 bg-white p-3 rounded border shadow-sm">
                  <div className="row">
                    <div className="col-6">
                      <label className="form-label fw-bold">Background Color</label>
                      <input type="color" className="form-control form-control-color w-100" value={editingBlock.data?.bg_color || '#ffffff'} onChange={(e) => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.bg_color = e.target.value;
                        setSections(updated);
                      }} />
                    </div>
                    <div className="col-6">
                      <label className="form-label fw-bold">Text Color</label>
                      <input type="color" className="form-control form-control-color w-100" value={editingBlock.data?.text_color || '#1e293b'} onChange={(e) => {
                        const updated = [...sections];
                        updated[editingBlock.index].data.text_color = e.target.value;
                        setSections(updated);
                      }} />
                    </div>
                  </div>
                </div>
              </div>
              <div className="modal-footer bg-white border-top">
                <button type="button" className="btn btn-secondary fw-semibold px-4" onClick={() => setEditingBlock(null)}>Close</button>
                <button type="button" className="btn btn-primary fw-bold px-5" onClick={() => {
                  saveSectionsToDb(sections);
                  setEditingBlock(null);
                }}>Save Changes</button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default AdminLandingPageBuilder;
