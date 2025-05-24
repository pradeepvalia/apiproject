import CryptoJS from 'crypto-js';
import axios from 'axios';

let publicKey = null;

/**
 * Initialize the encryption by fetching the public key
 */
export const initializeEncryption = async () => {
    try {
        const response = await axios.get('/api/encryption/public-key');
        publicKey = response.data.public_key;
    } catch (error) {
        console.error('Failed to fetch encryption key:', error);
        throw new Error('Failed to initialize encryption');
    }
};

/**
 * Encrypts a password using AES encryption
 * @param {string} password - The password to encrypt
 * @returns {string} - The encrypted password
 */
export const encryptPassword = (password) => {
    if (!publicKey) {
        throw new Error('Encryption not initialized. Call initializeEncryption() first.');
    }

    // Encrypt the password using AES encryption with the public key
    const encryptedPassword = CryptoJS.AES.encrypt(password, publicKey).toString();

    return encryptedPassword;
};

/**
 * Decrypts an encrypted password (for testing purposes only)
 * @param {string} encryptedPassword - The encrypted password
 * @returns {string} - The decrypted password
 */
export const decryptPassword = (encryptedPassword) => {
    if (!publicKey) {
        throw new Error('Encryption not initialized. Call initializeEncryption() first.');
    }

    // Decrypt the password
    const bytes = CryptoJS.AES.decrypt(encryptedPassword, publicKey);
    const decryptedPassword = bytes.toString(CryptoJS.enc.Utf8);

    return decryptedPassword;
};
