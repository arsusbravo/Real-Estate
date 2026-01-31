import type { User } from './auth';

// Property types
export type PropertyType = 'rumah' | 'apartemen' | 'tanah' | 'ruko' | 'gudang' | 'kantor';
export type ListingType = 'dijual' | 'disewakan';
export type CertificateType = 'shm' | 'shgb' | 'shp' | 'girik' | 'strata_title';
export type FurnishingType = 'unfurnished' | 'semi_furnished' | 'fully_furnished';
export type PropertyStatus = 'draft' | 'pending_review' | 'active' | 'sold' | 'rented' | 'inactive' | 'rejected';

export type Property = {
    id: number;
    uuid: string;
    slug: string;
    seller_id: number;
    title: string;
    description: string;
    property_type: PropertyType;
    listing_type: ListingType;
    price: number;
    price_formatted?: string;
    price_negotiable: boolean;
    land_area: number;
    building_area: number | null;
    bedrooms: number | null;
    bathrooms: number | null;
    floors: number;
    parking_spaces: number | null;
    furnishing: FurnishingType;
    facing_direction: string | null;
    province: string;
    city: string;
    district: string;
    subdistrict: string | null;
    address: string;
    postal_code: string | null;
    latitude: number | null;
    longitude: number | null;
    certificate_type: CertificateType;
    certificate_number: string | null;
    certificate_expiry: string | null;
    status: PropertyStatus;
    featured: boolean;
    verified: boolean;
    rejection_reason: string | null;
    published_at: string | null;
    sold_at: string | null;
    created_at: string;
    updated_at: string;

    // Relations
    seller?: User;
    features?: PropertyFeature[];
    media?: Media[];
    images_count?: number;
    inquiries_count?: number;
    favorites_count?: number;
    viewings_count?: number;
    views_count?: number;
};

export type PropertyFeature = {
    id: number;
    property_id: number;
    feature_name: string;
};

export type Media = {
    id: number;
    uuid: string;
    file_name: string;
    original_url: string;
    preview_url?: string;
    order_column: number;
    size: number;
    custom_properties?: Record<string, unknown>;
};

// Transaction types
export type TransactionStatus =
    | 'inquiry' | 'viewing_scheduled' | 'viewing_completed'
    | 'negotiation' | 'agreement_reached'
    | 'dp_pending' | 'dp_received'
    | 'document_collection' | 'document_verification'
    | 'notary_assigned' | 'notary_review'
    | 'ajb_preparation' | 'ajb_signing' | 'ajb_signed'
    | 'payment_processing' | 'payment_completed'
    | 'certificate_transfer' | 'certificate_completed'
    | 'handover' | 'completed'
    | 'on_hold' | 'cancelled' | 'disputed';

export type Transaction = {
    id: number;
    uuid: string;
    transaction_number: string;
    property_id: number;
    buyer_id: number;
    seller_id: number;
    notary_id: number | null;
    assigned_admin_id: number | null;
    agreed_price: number | null;
    dp_amount: number | null;
    commission_percentage: number;
    commission_amount: number | null;
    status: TransactionStatus;
    status_label?: string;
    status_color?: string;
    current_step: number;
    inquiry_date: string | null;
    viewing_date: string | null;
    negotiation_started_at: string | null;
    agreement_date: string | null;
    dp_paid_at: string | null;
    notary_assigned_at: string | null;
    ajb_signed_at: string | null;
    certificate_transferred_at: string | null;
    completed_at: string | null;
    cancelled_at: string | null;
    cancellation_reason: string | null;
    notes: string | null;
    created_at: string;
    updated_at: string;

    // Relations
    property?: Property;
    buyer?: User;
    seller?: User;
    notary?: NotaryPartner;
    activities?: TransactionActivity[];
    documents?: Document[];
};

export type TransactionActivity = {
    id: number;
    transaction_id: number;
    user_id: number;
    activity_type: string;
    description: string;
    old_value: Record<string, unknown> | null;
    new_value: Record<string, unknown> | null;
    created_at: string;

    // Relations
    user?: User;
};

// Notary types
export type NotaryPartner = {
    id: number;
    name: string;
    license_number: string;
    office_name: string;
    address: string;
    phone: string;
    email: string;
    city: string;
    specializations: string[];
    is_active: boolean;
    notes: string | null;
    created_at: string;
    updated_at: string;
    transactions_count?: number;
};

// Document types
export type DocumentStatus = 'uploaded' | 'under_review' | 'verified' | 'rejected' | 'expired';

export type Document = {
    id: number;
    uuid: string;
    documentable_type: string;
    documentable_id: number;
    uploaded_by: number;
    document_type: string;
    document_type_label?: string;
    category: string;
    file_path: string;
    file_name: string;
    file_size: number;
    mime_type: string | null;
    status: DocumentStatus;
    verified_by: number | null;
    verified_at: string | null;
    rejection_reason: string | null;
    expiry_date: string | null;
    notes: string | null;
    created_at: string;
    updated_at: string;

    // Relations
    uploader?: User;
    verifier?: User;
};

// Inquiry types
export type InquiryStatus = 'new' | 'contacted' | 'converted' | 'closed';

export type Inquiry = {
    id: number;
    uuid: string;
    property_id: number;
    user_id: number | null;
    name: string;
    email: string;
    phone: string | null;
    whatsapp: string | null;
    message: string;
    preferred_contact_method: 'phone' | 'whatsapp' | 'email';
    status: InquiryStatus;
    admin_notes: string | null;
    converted_to_transaction_id: number | null;
    created_at: string;
    updated_at: string;

    // Relations
    property?: Property;
    user?: User;
};

// Buyer requirement types
export type RequirementUrgency = 'segera' | '1_bulan' | '3_bulan' | '6_bulan' | 'fleksibel';

export type BuyerRequirement = {
    id: number;
    uuid: string;
    user_id: number;
    property_types: PropertyType[];
    listing_type: 'beli' | 'sewa';
    min_price: number;
    max_price: number;
    min_land_area: number | null;
    max_land_area: number | null;
    min_building_area: number | null;
    max_building_area: number | null;
    min_bedrooms: number | null;
    min_bathrooms: number | null;
    preferred_locations: string[];
    preferred_certificate_types: CertificateType[];
    urgency: RequirementUrgency;
    additional_notes: string | null;
    status: 'active' | 'matched' | 'closed';
    created_at: string;
    updated_at: string;

    // Relations
    user?: User;
};

// Pagination
export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Paginated<T> = {
    data: T[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
};

// Admin Dashboard stats
export type AdminDashboardStats = {
    total_properties: number;
    active_properties: number;
    pending_properties: number;
    total_transactions: number;
    active_transactions: number;
    completed_transactions: number;
    total_users: number;
    total_buyers: number;
    total_sellers: number;
    new_inquiries: number;
    pending_documents: number;
    total_revenue: number;
};
